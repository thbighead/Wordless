<?php

namespace Wordless\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Wordless\Adapters\WordlessCommand;
use Wordless\Helpers\Environment;
use Wordless\Helpers\Str;

class WordlessInstall extends WordlessCommand
{
    protected static $defaultName = 'wordless:install';

    private QuestionHelper $questionHelper;
    private InputInterface $input;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setup($input, $output);

        $this->downloadWpCore();
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
        return [];
    }

    private function activateWpPlugins()
    {
        $this->runWpCliCommand('plugin activate --all');
    }

    private function downloadWpCore()
    {
        $wp_version = Environment::get('WP_VERSION', 'latest');

        $this->runWpCliCommand("core download --version=$wp_version --allow-root --skip-content");
    }

    private function flushWpRewriteRules()
    {
        $this->runWpCliCommand('rewrite flush --hard');
    }

    private function installWpCore()
    {
        $app_url = Str::finishWith($_ENV['APP_URL'] ?? $this->questionHelper->ask(
                $this->input,
                $this->output,
                new Question('APP_URL value into .env not found. Please, provide a valid URL to project:')
            ), '/');
        $app_name = Environment::get('APP_NAME', 'Wordless App');
        $wp_admin_email = Environment::get('WP_ADMIN_EMAIL', 'php-team@infobase.com.br');
        $wp_admin_password = Environment::get('WP_ADMIN_PASSWORD', 'infobase123');
        $wp_admin_user = Environment::get('WP_ADMIN_USER', 'infobase');

        $this->runWpCliCommand(
            "core install --url=$app_url --title=$app_name --admin_user=$wp_admin_user --admin_password=$wp_admin_password --admin_email=$wp_admin_email"
        );
        $this->runWpCliCommand("option update siteurl {$app_url}wp-cms/wp-core/");
    }

    private function installWpCorePtBrLanguage()
    {
        $this->runWpCliCommand('language core install pt_BR --activate');
    }

    private function installWpPluginsPtBrLanguage()
    {
        $this->runWpCliCommand('language plugin install pt_BR --all --allow-root');
    }

    private function makeWpBlogPublic()
    {
        $blog_public = Environment::get('APP_ENV') === Environment::PRODUCTION ? '1' : '0';

        $this->runWpCliCommand("option update blog_public $blog_public");
    }

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
        $this->input = $input;
        $this->output = $output;
        $this->wpCliCommand = $this->getApplication()->find(WpCliCaller::COMMAND_NAME);
    }
}
