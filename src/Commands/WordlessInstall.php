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
use Wordless\Exception\FailedToRewriteDotEnvFile;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\Environment;
use Wordless\Helpers\ProjectPath;
use Wordless\Helpers\Str;

class WordlessInstall extends WordlessCommand
{
    protected static $defaultName = 'wordless:install';
    private const FORCE_MODE = 'force';
    private const NO_ASK_MODE = 'no-ask';
    private const WORDPRESS_SALT_FILLABLE_VALUE = '$WORDPRESS_SALT_AUTO_GENERATE';

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
     * @throws FailedToCopyDotEnvExampleIntoNewDotEnv
     * @throws FailedToRewriteDotEnvFile
     * @throws PathNotFoundException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setup($input, $output);

        $this->resolveDotEnv();

        $this->downloadWpCore();
        $this->createWpConfigFromStub();
        $this->createWpDatabase();
        $this->installWpCore();
        $this->installWpCorePtBrLanguage();
        $this->flushWpRewriteRules();
        $this->activateWpPlugins();
        $this->installWpPluginsPtBrLanguage();
        $this->makeWpBlogPublic();

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
     * @throws FailedToCopyDotEnvExampleIntoNewDotEnv
     * @throws PathNotFoundException
     */
    private function createWpConfigFromStub()
    {
        $filename = 'wp-config.php';

        if (!copy(
            $wp_config_stub_filepath = ProjectPath::root("stubs/$filename"),
            $new_wp_config_filepath = ProjectPath::wpCore() . "/$filename"
        )) {
            throw new FailedToCopyDotEnvExampleIntoNewDotEnv(
                $wp_config_stub_filepath,
                $new_wp_config_filepath
            );
        }
    }

    /**
     * @throws Exception
     */
    private function createWpDatabase()
    {
        $database_username = Environment::get('DB_USER');
        $database_password = Environment::get('DB_PASSWORD');

        $this->runWpCliCommand("db create --dbuser=$database_username --dbpass=$database_password");
    }

    /**
     * @throws Exception
     */
    private function downloadWpCore()
    {
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
        $dot_env_content = file_get_contents($dot_env_filepath);

        $dot_env_content = $this->guessAndResolveDotEnvWpSaltVariables($dot_env_content);

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
        $this->runWpCliCommand('rewrite flush --hard');
    }

    private function getDotEnvNotFilledVariables(string $dot_env_content): array
    {
        preg_match_all('/.+=(\$[^\W]+)\W/', $dot_env_content, $not_filled_variables_regex_result);
        // Getting Regex result (\$[^\W]+) group or leading to an empty array
        return $not_filled_variables_regex_result[1] ?? [];
    }

    /**
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
        if (!str_contains($dot_env_content, self::WORDPRESS_SALT_FILLABLE_VALUE)) {
            return $dot_env_content;
        }

        $wp_salt_response = HttpClient::create()->request(
            'GET',
            'https://api.wordpress.org/secret-key/1.1/salt/'
        )->getContent();

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

        return $this->ask(
            "What should be $variable_name value? [$variable_default]",
            $variable_default
        );
    }

    /**
     * @throws Exception
     */
    private function installWpCore()
    {
        $app_url = Str::finishWith($_ENV['APP_URL'], '/');
        $app_name = Environment::get('APP_NAME', 'Wordless App');
        $wp_admin_email = Environment::get('WP_ADMIN_EMAIL', 'php-team@infobase.com.br');
        $wp_admin_password = Environment::get('WP_ADMIN_PASSWORD', 'infobase123');
        $wp_admin_user = Environment::get('WP_ADMIN_USER', 'infobase');

        $this->runWpCliCommand(
            "core install --url=$app_url --title=$app_name --admin_user=$wp_admin_user --admin_password=$wp_admin_password --admin_email=$wp_admin_email"
        );
        $this->runWpCliCommand("option update siteurl {$app_url}wp-cms/wp-core/");
    }

    /**
     * @throws Exception
     */
    private function installWpCorePtBrLanguage()
    {
        $this->runWpCliCommand('language core install pt_BR --activate');
    }

    /**
     * @throws Exception
     */
    private function installWpPluginsPtBrLanguage()
    {
        $this->runWpCliCommand('language plugin install pt_BR --all --allow-root');
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

            $filler_dictionary[$variable] = $variable_value;
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
     * @param string $command
     * @throws Exception
     */
    private function runWpCliCommand(string $command)
    {
        if ($return_var = $this->wpCliCommand->run(new ArrayInput([
            WpCliCaller::WP_CLI_FULL_COMMAND_STRING_ARGUMENT_NAME => $command,
        ]), $this->output)) {
            exit($return_var);
        }
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
}
