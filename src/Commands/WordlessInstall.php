<?php

namespace Wordless\Commands;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Wordless\Adapters\WordlessCommand;
use Wordless\Exception\FailedToCopyDotEnvExampleIntoNewDotEnv;
use Wordless\Exception\FailedToCopyStub;
use Wordless\Exception\FailedToDeletePath;
use Wordless\Exception\FailedToRewriteDotEnvFile;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\DirectoryFiles;
use Wordless\Helpers\Environment;
use Wordless\Helpers\ProjectPath;
use Wordless\Helpers\Str;

class WordlessInstall extends WordlessCommand
{
    protected static $defaultName = 'wordless:install';
    private const FORCE_MODE = 'force';
    private const NO_ASK_MODE = 'no-ask';
    private const WORDPRESS_SALT_FILLABLE_VALUES = [
        '$AUTH_KEY',
        '$SECURE_AUTH_KEY',
        '$LOGGED_IN_KEY',
        '$NONCE_KEY',
        '$AUTH_SALT',
        '$SECURE_AUTH_SALT',
        '$LOGGED_IN_SALT',
        '$NONCE_SALT',
    ];
    private const WORDPRESS_SALT_URL_GETTER = 'https://api.wordpress.org/secret-key/1.1/salt/';

    private QuestionHelper $questionHelper;
    private InputInterface $input;
    private array $modes;
    private OutputInterface $output;
    private Command $wpCliCommand;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function arguments(): array
    {
        return [];
    }

    protected function description(): string
    {
        return 'Install project.';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ClientExceptionInterface
     * @throws Exception
     * @throws FailedToCopyStub
     * @throws FailedToRewriteDotEnvFile
     * @throws PathNotFoundException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setup($input, $output);

        $this->resolveForceMode();

        $this->resolveDotEnv();

        $this->downloadWpCore();
        $this->createWpConfigFromStub();
        $this->createRobotsTxtFromStub();
        $this->createWpDatabase();
        $this->installWpCore();
        $this->flushWpRewriteRules();
        $this->activateWpTheme();
        $this->activateWpPlugins();
        $this->installWpPluginsPtBrLanguage();
        $this->installWpCorePtBrLanguage();
        $this->makeWpBlogPublic();
        $this->switchingMaintenanceMode(false);

        $this->resolveWpConfigChmod();

        return Command::SUCCESS;
    }

    protected function help(): string
    {
        return 'Completely installs this project calling WP-CLI.';
    }

    protected function options(): array
    {
        return [
            [
                self::OPTION_NAME_FIELD => self::FORCE_MODE,
                self::OPTION_SHORTCUT_FIELD => 'f',
                self::OPTION_MODE_FIELD => InputOption::VALUE_NONE,
                self::OPTION_DESCRIPTION_FIELD => 'Forces a project reinstallation.',
            ],
            [
                self::OPTION_NAME_FIELD => self::NO_ASK_MODE,
                self::OPTION_MODE_FIELD => InputOption::VALUE_NONE,
                self::OPTION_DESCRIPTION_FIELD => 'Don\'t ask for any input while running.',
            ],
        ];
    }

    private function activateWpTheme()
    {
        $this->runWpCliCommand('theme activate ' . Environment::get('WP_THEME', 'wordless'));
    }

    /**
     * @throws Exception
     */
    private function activateWpPlugins()
    {
        $this->runWpCliCommand('plugin activate --all');
    }

    private function ask(string $question, $default = null)
    {
        return $this->questionHelper->ask($this->input, $this->output, new Question($question, $default));
    }

    /**
     * @throws PathNotFoundException
     */
    private function createRobotsTxtFromStub()
    {
        $filename = 'robots.txt';
        $new_robots_txt_filepath = ProjectPath::publicHtml() . "/$filename";

        if (($supposed_already_existing_robots_txt_filepath = realpath($new_robots_txt_filepath)) !== false) {
            if ($this->output->isVerbose()) {
                $this->output->writeln(
                    "$supposed_already_existing_robots_txt_filepath already exists, skipping."
                );
            }
            return;
        }

        $robots_txt_content = file_get_contents(ProjectPath::stubs($filename));

        preg_match_all('/{(\S+)}/', $robots_txt_content, $replaceable_values_regex_result);
        $env_variables_to_replace_into_robots_txt_stub = $replaceable_values_regex_result[1] ?? [];

        if (empty($env_variables_to_replace_into_robots_txt_stub)) {
            file_put_contents($new_robots_txt_filepath, $robots_txt_content);
            return;
        }

        $robots_txt_content = str_replace(
            $replaceable_values_regex_result[0] ?? [],
            array_map(function ($env_variable_name) {
                $env_variable_value = Environment::get($env_variable_name, '');

                return str_contains($env_variable_name, 'URL') ?
                    Str::finishWith($env_variable_value, '/') : $env_variable_value;
            }, $env_variables_to_replace_into_robots_txt_stub),
            $robots_txt_content
        );

        file_put_contents($new_robots_txt_filepath, $robots_txt_content);
    }

    /**
     * @throws FailedToCopyStub
     * @throws PathNotFoundException
     */
    private function createWpConfigFromStub()
    {
        $filename = 'wp-config.php';
        $new_wp_config_filepath = ProjectPath::wpCore() . "/$filename";

        if ((($supposed_already_existing_wp_config_filepath = realpath($new_wp_config_filepath)) !== false)
            && str_contains(file_get_contents($new_wp_config_filepath), '@author Wordless')) {
            if ($this->output->isVerbose()) {
                $this->output->writeln(
                    "Wordless seems to already have created a config file at $supposed_already_existing_wp_config_filepath, skipping."
                );
            }

            return;
        }

        if (!copy($wp_config_stub_filepath = ProjectPath::stubs($filename), $new_wp_config_filepath)) {
            throw new FailedToCopyStub($wp_config_stub_filepath, $new_wp_config_filepath);
        }
    }

    /**
     * @throws Exception
     */
    private function createWpDatabase()
    {
        $database_username = Environment::get('DB_USER');
        $database_password = Environment::get('DB_PASSWORD');

        if ($this->runWpCliCommand(
                "db check --dbuser=$database_username --dbpass=$database_password",
                true
            ) == 0) {
            if ($this->output->isVerbose()) {
                $this->output->writeln('WordPress Database already created, skipping.');
            }

            return;
        }

        $this->runWpCliCommand("db create --dbuser=$database_username --dbpass=$database_password");
    }

    /**
     * @throws Exception
     */
    private function downloadWpCore()
    {
        if ($this->runWpCliCommand("core version --extra", true) == 0) {
            if ($this->output->isVerbose()) {
                $this->output->writeln('WordPress Core already downloaded, skipping.');
            }

            return;
        }

        $wp_version = Environment::get('WP_VERSION', 'latest');

        $this->runWpCliCommand("core download --version=$wp_version --allow-root --skip-content");
    }

    /**
     * @param string $dot_env_filepath
     * @throws ClientExceptionInterface
     * @throws FailedToRewriteDotEnvFile
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function fillDotEnv(string $dot_env_filepath)
    {
        $dot_env_content = $this->guessAndResolveDotEnvWpSaltVariables(file_get_contents($dot_env_filepath));

        if (empty($not_filled_variables = $this->getDotEnvNotFilledVariables($dot_env_content))) {
            return;
        }

        $this->output->writeln(
            "We'll need to fill up $dot_env_filepath: ('null' values to comment line)"
        );

        $filler_dictionary = $this->mountDotEnvFillerDictionary($not_filled_variables);
        $dot_env_content = str_replace(
            array_keys($filler_dictionary),
            array_values($filler_dictionary),
            $dot_env_content
        );

        if (file_put_contents($dot_env_filepath, $dot_env_content) === false) {
            throw new FailedToRewriteDotEnvFile($dot_env_filepath, $dot_env_content);
        }
    }

    /**
     * @throws Exception
     */
    private function flushWpRewriteRules()
    {
        $permalink_structure = Environment::get('WP_PERMALINK', '/%postname%/');
        $this->runWpCliCommand("rewrite structure $permalink_structure --hard");
        $this->runWpCliCommand('rewrite flush --hard');
    }

    private function getDotEnvNotFilledVariables(string $dot_env_content): array
    {
        preg_match_all('/.+=(\$[^\W]+)\W/', $dot_env_content, $not_filled_variables_regex_result);
        // Getting Regex result (\$[^\W]+) group or leading to an empty array
        return $not_filled_variables_regex_result[1] ?? [];
    }

    /**
     * @return string
     * @throws FailedToCopyDotEnvExampleIntoNewDotEnv
     * @throws PathNotFoundException
     */
    private function getOrCreateDotEnvFilepath(): string
    {
        if (!DOT_ENV_NOT_LOADED) {
            return ProjectPath::root('.env');
        }

        if (!copy(
            $dot_env_example_filepath = ProjectPath::root('.env.example'),
            $new_dot_env_filepath = ProjectPath::root() . '/.env'
        )) {
            throw new FailedToCopyDotEnvExampleIntoNewDotEnv(
                $dot_env_example_filepath,
                $new_dot_env_filepath
            );
        }

        return ProjectPath::realpath($new_dot_env_filepath);
    }

    /**
     * @param string $dot_env_content
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function guessAndResolveDotEnvWpSaltVariables(string $dot_env_content): string
    {
        if (!Str::contains($dot_env_content, self::WORDPRESS_SALT_FILLABLE_VALUES)) {
            return $dot_env_content;
        }

        if ($this->output->isVerbose()) {
            $this->output->write('Retrieving WP SALTS at ' . self::WORDPRESS_SALT_URL_GETTER . '... ');
        }

        $wp_salt_response = HttpClient::create()->request(
            'GET',
            self::WORDPRESS_SALT_URL_GETTER
        )->getContent();

        if ($this->output->isVerbose()) {
            $this->output->writeln('Done!');
        }

        preg_match_all(
            '/define\(\'(.+)\',.+\'(.+)\'\);/',
            $wp_salt_response,
            $parse_wp_salt_response_regex_result
        );

        return str_replace(
            array_map(function ($env_variable_name) {
                return "\$$env_variable_name";
            }, $parse_wp_salt_response_regex_result[1] ?? []),
            array_map(function ($salt_value) {
                return "\"$salt_value\"";
            }, $parse_wp_salt_response_regex_result[2] ?? []),
            $dot_env_content
        );
    }

    private function guessOrAskDotEnvVariableValue(string $variable_marked_as_not_filled)
    {
        $variable_default = $_ENV[$variable_marked_as_not_filled] ?? '';

        if ($this->modes[self::NO_ASK_MODE]) {
            return $variable_default;
        }

        $variable_name = substr($variable_marked_as_not_filled, 1); // removing '$'
        $variable_default = $_ENV[$variable_marked_as_not_filled] ??
            Environment::COMMONLY_DOT_ENV_DEFAULT_VALUES[$variable_name] ??
            '';

        return $this->ask(
            "What should be $variable_name value? [$variable_default] ",
            $variable_default
        );
    }

    /**
     * @throws Exception
     */
    private function installWpCore()
    {
        if ($this->runWpCliCommand("core is-installed", true) == 0) {
            if ($this->output->isVerbose()) {
                $this->output->writeln('WordPress Core already installed, minor updating.');
            }

            $this->switchingMaintenanceMode(true);

            if (Environment::get('WP_VERSION', 'latest') === 'latest') {
                $this->runWpCliCommand('core update --minor --allow-root');
            }

            return;
        }

        $app_url = Str::finishWith($_ENV['APP_URL'], '/');
        $app_name = Environment::get('APP_NAME', 'Wordless App');
        $wp_admin_email = Environment::get('WP_ADMIN_EMAIL', 'php-team@infobase.com.br');
        $wp_admin_password = Environment::get('WP_ADMIN_PASSWORD', 'infobase123');
        $wp_admin_user = Environment::get('WP_ADMIN_USER', 'infobase');

        $this->runWpCliCommand(
            "core install --url=$app_url --title=\"$app_name\" --admin_user=$wp_admin_user --admin_password=$wp_admin_password --admin_email=$wp_admin_email"
        );
        $this->switchingMaintenanceMode(true);
        $this->runWpCliCommand("option update siteurl {$app_url}wp-cms/wp-core/");
    }

    /**
     * @throws Exception
     */
    private function installWpCorePtBrLanguage()
    {
        if ($this->runWpCliCommand("language core is-installed pt_BR", true) == 0) {
            if ($this->output->isVerbose()) {
                $this->output->writeln('WordPress Core Language pt_BR already installed, updating.');
            }

            $this->runWpCliCommand('language core update', true);
            $this->runWpCliCommand('language core activate pt_BR', true);

            return;
        }

        $this->runWpCliCommand('language core install pt_BR --activate');
    }

    /**
     * @throws Exception
     */
    private function installWpPluginsPtBrLanguage()
    {
        $this->runWpCliCommand('language plugin install pt_BR --all --allow-root', true);
        $this->runWpCliCommand('language plugin update pt_BR --all --allow-root', true);
    }

    /**
     * @throws Exception
     */
    private function makeWpBlogPublic()
    {
        $blog_public = Environment::get('APP_ENV') === Environment::PRODUCTION ? '1' : '0';

        $this->runWpCliCommand("option update blog_public $blog_public");
    }

    private function mountDotEnvFillerDictionary(array $not_filled_variables): array
    {
        $filler_dictionary = [];

        foreach ($not_filled_variables as $variable) {
            $variable_value = $this->guessOrAskDotEnvVariableValue($variable);

            if ($variable_value === 'null') {
                $variable_name = substr($variable, 1); // removing '$'
                $filler_dictionary["$variable_name=$variable"] = "#$variable_name=";
                continue;
            }

            $filler_dictionary[$variable] = "\"$variable_value\"";
        }

        return $filler_dictionary;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws FailedToCopyDotEnvExampleIntoNewDotEnv
     * @throws FailedToRewriteDotEnvFile
     * @throws PathNotFoundException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function resolveDotEnv()
    {
        $this->fillDotEnv($this->getOrCreateDotEnvFilepath());
    }

    /**
     * @throws PathNotFoundException
     * @throws FailedToDeletePath
     */
    private function resolveForceMode()
    {
        if ($this->input->getOption('force')) {
            DirectoryFiles::recursiveDelete(
                ProjectPath::wpCore(),
                [ProjectPath::wpCore('.gitignore')],
                false
            );

            DirectoryFiles::delete(ProjectPath::publicHtml('robots.txt'));
        }
    }

    /**
     * @throws PathNotFoundException
     */
    private function resolveWpConfigChmod()
    {
        if (Environment::get('APP_ENV') === Environment::PRODUCTION) {
            chmod(ProjectPath::wpCore('wp-config.php'), 660);
        }
    }

    /**
     * @param string $command
     * @param bool $return_script_code
     * @return int
     * @throws Exception
     */
    private function runWpCliCommand(string $command, bool $return_script_code = false): int
    {
        if (($return_var = $this->wpCliCommand->run(new ArrayInput([
                WpCliCaller::WP_CLI_FULL_COMMAND_STRING_ARGUMENT_NAME => $command,
            ]), $this->output)) && !$return_script_code) {
            exit($return_var);
        }

        return $return_var;
    }

    private function setup(InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $this->getHelper('question');
        $this->modes = [
            self::FORCE_MODE => $input->getOption(self::FORCE_MODE),
            self::NO_ASK_MODE => $input->getOption(self::NO_ASK_MODE),
        ];
        $this->input = $input;
        $this->output = $output;
        $this->wpCliCommand = $this->getApplication()->find(WpCliCaller::COMMAND_NAME);
    }

    /**
     * @param bool $switch
     * @throws Exception
     */
    private function switchingMaintenanceMode(bool $switch)
    {
        $switch = $switch ? 'activate' : 'deactivate';

        $this->runWpCliCommand("maintenance-mode $switch");
    }
}
