# Wordless

A Headless WordPress Project **for developers** who are tired of WordPress

- [Do It Quick](#do-it-quick)
- [Download](#download-wordless)
    - [Installation](#install-wordless)
- [WordPress discussion](#about-developers-and-wordpress)
- [Change WordPress Version](#change-wordpress-version)
- [Wordless Project Directories](#directory-and-files-organization)
- [Wordless CLI](#wordless-cli)
    - [Running WP-CLI](#running-wp-cli-commands)
- [WordPress Plugins](#wordpress-plugins)
- [Common Issues](#common-issues)

## Do It Quick

### New project

```shell
composer create-project thbighead/wordless example-app
cd example-app
cp .env.example .env
cp docker/nginx/sites/app.conf.example docker/nginx/sites/app.conf
```

- _Edit `.env` line 3 with your application host value._
- _Edit `docker/nginx/sites/app.conf` line 11 exactly as `.env` `APP_HOST` value._
- _Add your host into your local hosts file (it depends on your OS)._

```shell
docker compose up -d
docker compose exec --user=laradock workspace bash
```

> Now inside your workspace container:
> ```shell
> composer install
> php console wordless:install
> ```

### Initializing cloned project

```shell
cp .env.example .env
cp docker/nginx/sites/app.conf.example docker/nginx/sites/app.conf
```

- _Edit `.env` line 3 with your application host value._
- _Edit `docker/nginx/sites/app.conf` line 11 exactly as `.env` `APP_HOST` value._
- _Add your host into your local hosts file (it depends on your OS)._

```shell
docker compose up -d
docker compose exec --user=laradock workspace bash
```

> Now inside your workspace container:
> ```shell
> composer install
> php console wordless:install
> ```

## Download Wordless

Just run the following code:

```
composer create-project thbighead/wordless example-app
cd example-app
```


### Wordless Docker

> *As you can see, we built these containers based on [Laradock project](https://laradock.io/).*

After download, you should start the default containers available by Wordless. To achieve it, first you should create
a new `docker/nginx/sites/app.conf` copying from `docker/nginx/sites/app.conf.example`, then, just edit line 11 with
your desired application URL as the server name. **That should be the same value you'll set as `APP_HOST` into your
`.env` file that you'll discuss later.**

Next, just run `docker compose up -d` (we recommend
[Docker Compose version 2](https://docs.docker.com/compose/#compose-v2-and-the-new-docker-compose-command)).

Now to continue Wordless installation we recommend use the built in `workspace` container which you may access through
the following command:

```shell
docker compose exec --user=laradock workspace bash
```

There you go. Inside this container you may install the project using [`wordless:install`](#install-wordless) command.

## About developers and WordPress

> *If you're a WordPress fan make sure you understand WordPress **isn't** any kind of tool made for developers.
> We'll discuss it further bellows.*

> *If you're a PHP developer make sure you understand WordPress **is** old and still lives by getting massive
> updates frequently. You shouldn't be afraid about old code, you can nail then: you just need your time.*

WordPress is a powerful [CMS](https://en.wikipedia.org/wiki/Content_management_system) which grew up for many years and
nowadays can give any non-IT person an opportunity to fully create and customize a blog-like website. But more than just
maintain a content, the admin panel makes you powerful to even change the whole page code. There is where the magic, yet
the problem, starts.

WordPress is made upon plugins and themes which don't need to follow any kind of project structure. All you need to do
is take an adventure through WordPress hooks, sometimes called filters and/or actions and do what you want to do.
Although WordPress cares to introduce some concepts to organize and name theme files like excerpt, post, pages,
categories, taxonomies, etc., each theme have a completely different directory organization and project structure.
Finally, when talking about plugins, project organization goes even more complicated, and we realize that even
WordPress is being constantly updated its code still lacks some refactoring.

Well, does WordPress sucks then? ***No***.

In the end of the day it works like a charm. It's just not a tool to help developers! Instead, WordPress is an
extensible CMS application with a well known admin panel. All we, IT-people, must understand is that it should be
respected as it is.

The whole problem for developers is that a change of behavior in WordPress may be done in literally anywhere. Seriously.
Anywhere. So when you ask us to change a color of something in site, it has maybe done in CSS file, PHP file, into
database managed from admin panel, or a CLI command. Even a JS file maybe changing it. There's no rule, no layers to
begin to search on, and we are talking just about a color change. Now imagine an API consumption to fill up a form for
users which logged in using their Facebook account if they and only if they have never logged in by other ways before.

All that said, a great way to begin working better with WordPress is to define those layers, which we begin by
separating the view from business codes. A great way to achieve it is by using it as a
[**Headless** CMS](https://en.wikipedia.org/wiki/Headless_content_management_system).

## Wordless strategy

Our **mantra** here is:

> *Let WordPress works like an app and develop your custom features elsewhere.*

To achieve it we install WordPress as a Headless CMS, which means it has no more responsibility to manage page views.
Instead, we shall use [WordPress REST API](https://developer.wordpress.org/rest-api/) to serve a front-end project as
client. Also, to prepare the whole WordPress project we count with a console installer made with
[Composer](https://getcomposer.org/), [Symfony](https://symfony.com/doc/current/index.html), and
[WP-CLI](https://developer.wordpress.org/cli/commands/).

Wordless introduces a blank theme which does just... well... nothing. But you may even install any known (or unknown)
theme and use it to serve your content through web.

### WordPressic Theme

Sometimes you may wish to build projects using Wordless as your backend serving pages just like any WordPress project
would do, but with all Wordless tools. To achieve this we have another theme which extends the blank theme called SSR
(just like the well known Server Side Rendering "strategy" from the most known frontend frameworks).

### Directories and files organization

```
| app (Composer initializes everything inside this directory)
| \
|  | Commands (Custom project commands to run through console)
|  | Controllers (Custom project Controllers to add routes to API)
|  | Hookers (WordPress action/filter done through an easy-to-use class)
|  | \
|  |  | Ajax (Wordless hookers for easy defining WordPress AJAX functions)
|  | Menus (WordPress menu registration classes)
|  | Scripts (WordPress front-end scripts registration classes)
|  | Styles (WordPress front-end styles registration classes)
| cache (Internal cache files)
| config (Published project configuration files)
| docker (Easy to use development environment containers based on Laradock)
| \
|  | adminer (Adminer container for database access through browser GUI)
|  | logs (Where all container log files shall be created)
|  | mariadb (MariaDB container as our default database)
|  | php-fpm (PHP container as our default programming language)
|  | workspace (Our developing container which we access to use Composer, NPM, etc.)
|  | nginx (MariaDB container as our default database)
|  | \
|  |  | sites (Where you should place your app config file for NGINX)
|  |  | \
|  |  |  | ssl (where your app cresencial for SSL access (HTTPS) shall be generated)
|  |  |  |>app.conf.example (a good start of NGINX config file for your app)
| migrations (Where we store our migration files)
| packages (Used by Wordless oficial package development)
| public (Websystem entrypoint where we place some symbolic links to WordPress folders)
| \
|  |>robots.txt (auto-generated after wordless:install)
| wp
| \
|  | wp-content
|  | \
|  |  | languages (Just like WordPress. Everything inside is ignored by Git)
|  |  | mu-plugins (Just like WordPress. Place any handmade or modified plugin here)
|  |  | plugins (Just like WordPress. Everything inside is ignored by Git. Installation controlled by Composer)
|  |  | themes (Just like WordPress. Ships with wordless theme)
|  |  | uploads (For built-in WordPress filesystem. Everything inside is ignored by Git)
|  |  |>debug.log (WordPress log file)
|  | wp-core (WordPress core files (anything but wp-content))
|  |>index.php (Just like WordPress)
|>.env.example (Used to create new .env files)
|>composer.json (Composer)
|>console (Wordless CLI file)
|>docker-compose.yml (Docker Compose file to up application containers)
|>wp-cli.yml (WP-CLI config file)
```

### Wordless CLI

The `console` has many commands to help you install and keep your project running. To check what commands are available,
just run `php console list`. To have more info about each command run `php console {command:alias} --help`. You may
create your own commands at `Commands` directory extending `\Wordless\Infrastructure\ConsoleCommand`.

#### Running WP-CLI commands

By running `php console wp:run "command with arguments and options here"` Wordless shall execute any WP-CLI commands.
Just take care to use the quotes to surround the command, for example:

```
php console wp:run "cache flush"
```

Wordless keeps WP-CLI through Composer and will choose the correct script file to execute according to your Operational
System.

### Install Wordless

#### Preconfiguration files

Before running any commands, you shoud check for the following preconfiguraton files:

##### `.env`

`.env` is a file used to keep values that maybe different on each environment your project is running (for example if
you have a product, local and staging environments to your app). For this reason **IT SHOULD NEVER BE VERSIONED BY
GIT**. You're free to add more values as you need, but keep in mind that those already present into `.env.example`
are core values to Wordless.

You may access the `.env` values through your entire project using `Environment::get()` helper:

```php
use Wordless\Helpers\Environment;
// ...
$admin_mail = Environment::get('WP_ADMIN_EMAIL', 'A default value');
```

> **IMPORTANT:** as you can see `.env` defines the SALT values. They're automatically generated through
> https://api.wordpress.org/secret-key/1.1/salt/

##### `docker/nginx/sites/app.conf`

This file should be created using `docker/nginx/sites/app.conf.example` as base. **Just remember to please attempt at
its line 11, where the `server_name` should have the same value of your `.env` `APP_HOST` variable.**

##### `.htaccess`

As we use NGINX instead of Apache, those files are all ignored.

#### Running the `console` command

To a fresh start, after all Composer packages areinstalled through a `composer install` command, just run
`php console wordless:install` at project's root.

> If you want to check what's happening during this script run it with the `-v` flag: `php console wordless:install -v`.

This command will resolve the following in order:

##### Resolve force mode (`-f`)

This step is responsible to resolve the following needings when running the installation command with the `-f` flag:

- Remove `wp/wp-content/mu-plugins/wordless-plugin.php`;
- Remove `wp/wp-core/wp-config.php`;
- Remove `public/robots.txt`.

If any of that files does not exist, we just ignores it. If we fail to delete the file an Exception
`FailedToDeletePath` is raised.

##### Loads configurations

###### Fill `.env` with salts

The WordPress Salts are kept in `.env` variables and, if they aren't already evaluated, this part of the process shall
evaluate them. Also, the process already loads all environment variable in memory after this step. You can see more
[here](#env).

###### Load languages

Wordless will try to `explode` the `languages` key at `config/wordpress.php` returns through `,` character and install
them to plugins and core.

##### Publish files

> Publishing files are steps that copy files from a stub to a particular place with a particular name.

###### Publish `wp-config.php`

The process calls the `php console publish:wp-config.php` command to insert a new `wp-config.php` file at `wp/wp-core`.
If the file is already present, this step is skipped.

> ###### Custom `wp-config.php`
> 
> By default, this file shall be created based in `vendor/thbighead/wordless-framework/assets/stubs/wp-config.php`. If
> you want to change the `wp-config.php` behavior, create your customized one at `stubs/wp-config.php` and it shall be
> used instead by this process step.

###### Publish `robots.txt`

A basic `robots.txt` will be created based on `vendor/thbighead/wordless-framework/assets/stubs/robots_non_prod.txt`
or `vendor/thbighead/wordless-framework/assets/stubs/robots_prod.txt` if you don't have one inside `public`. You may
modify the stub file as you wish and even making references to `.env` values by surrounding them with brackets (just
like we did for the Sitemap value inside it). For this you just need to create a file with the same name just like we
do for [custom `wp-config.php`](#custom-wp-configphp)

###### Publish Wordless Must Use Plugin file

Wordless is loaded inside WordPress environment by a
[Must-Use Plugin](https://developer.wordpress.org/advanced-administration/plugins/mu-plugins/). This file stub should
be changed carefully. We recommend to never touch that, so Wordless can work properly.

##### Installs WordPress Database

With the database credentials filled into `.env`, Wordless is able to check if your server already has the necessary
database to proceed installation. If the database is not present, it first creates an **empty** (without tables) right
now.

After that, Wordless will check your database and repair it if necessary.

Everything here is done by a WP-CLI command, the following commands are/maybe used in order:

- [`db check`](https://developer.wordpress.org/cli/commands/db/check/);
- [`db create`](https://developer.wordpress.org/cli/commands/db/create/);
- [`core is-installed`](https://developer.wordpress.org/cli/commands/core/is-installed/);
- [`core install --url={As in .env APP_URL} --locale={First language listed} --title={As in .env APP_NAME} --admin_email={An invalid known e-mail} --admin_user=temp --skip-email`](https://developer.wordpress.org/cli/commands/core/install/)
according to your application configurations;

##### Core steps

> The following steps are done wrapped by maintenance mode. We control it through the following WP-CLI commands:
> - [`maintenance-mode activate`](https://developer.wordpress.org/cli/commands/maintenance-mode/activate/)
> - [`maintenance-mode deactivate`](https://developer.wordpress.org/cli/commands/maintenance-mode/deactivate/)

###### Fixing database URLs

The well known options `siteurl` and `home` are update based on your app configuration.

After that a database optimization is performed via WP-CLI.

So the following WP-CLI are executed in order:

- [`option update siteurl {As in .env APP_URL concatened with your admin panel URI configuration}`](https://developer.wordpress.org/cli/commands/option/update/)
- [`option update home {As in .env APP_URL}`](https://developer.wordpress.org/cli/commands/option/update/)
- [`db optimize`](https://developer.wordpress.org/cli/commands/db/optimize/)

###### Flushing rewrite rules

Based on your permalink configuration, the WP-CLI is called to flush older rules from database with the following
commands:

- [`rewrite structure '{As in your permalink configuration key}' --hard`](https://developer.wordpress.org/cli/commands/rewrite/structure/)
- [`rewrite flush --hard`](https://developer.wordpress.org/cli/commands/rewrite/flush/)

###### Activating Themes and Plugins

Activates the theme from `theme` key at `config/wordpress.php` returns. The default is our blank theme: `wordless`.

Then, activates all plugins.

Again, everything is controlled via WP-CLI commands as follows in order:

- [`theme activate {As in your theme configuration key}`](https://developer.wordpress.org/cli/commands/theme/activate/)
- [`plugin activate --all`](https://developer.wordpress.org/cli/commands/plugin/activate/)

###### Installing WordPress Languages

All Languages are installed for plugins, but for WordPress Core only the first one shall be installed, so **be sure to
choose carefully what language will be listed into it**. If the `languages` configuration key is empty this procedure
will be skipped.

Again, everything is controlled via WP-CLI commands as follows in order:

- [`language core is-installed {The very first locale listed in languages config key}`](https://developer.wordpress.org/cli/commands/language/core/is-installed/)
- [`language core install {The very first locale listed in languages config key} --activate`](https://developer.wordpress.org/cli/commands/language/core/install/)
- [`language core update`](https://developer.wordpress.org/cli/commands/language/core/update/)
- [`site switch-language {The very first locale listed in languages config key}`](https://developer.wordpress.org/cli/commands/site/switch-language/)
- For each _language_ listed in `languages` configuration key:
  - [`language plugin install {language} --all`](https://developer.wordpress.org/cli/commands/language/plugin/install/)
  - [`language plugin update {language} --all`](https://developer.wordpress.org/cli/commands/language/plugin/update/)

###### Making WordPress Blog Public

If your `.env` variable `APP_ENV` goes for `production` we set `blog_public` database value to `true`, otherwise we set
it to `false`. This is done by the [`option update blog_public {As explained previously}` WP-CLI command](https://developer.wordpress.org/cli/commands/option/update/)

###### Updating WordPress database

Performed by [WP-CLI `core update-db` command](https://developer.wordpress.org/cli/commands/core/update-db/)

###### Generating Symbolic Links

This step generates the symbolic links inside `public` directory as configured by `public-symlinks` key at
`config/wordless.php`. You can se more [here](#wordless-public-entrypoint-and-symlinks).

###### Applying WordPress Admin Configurations

The Admin Panel configurations are preconfigured here as follows:

- Configure date using `php console options:date` command. 

##### Registering Schedules

Register the WordPress CRON schedules using the `php console schedule:register` command. You may see more about
Schedules [here](#schedules).

##### Run Migrations

Runs the missing migrations into your `migrations` directory with the `php console migrate` command. See more about
migrations [here](#database-migrations).

##### Synchronizes User Roles

Synchronizes your application admin User Roles as in `permissions` configuration key at `config/wordpress.php`. See
more about Users Roles Synchronization [here](#users-roles-synchronization).

##### Generate internal caches

Through the command `php console cache:create` all configured caches are created at `cache` directory. You can see
more about Caches [here](#caches).

##### WordPress Configuration File Permissions

To avoid any problem when in production environment (`APP_ENV=production`) we set `wp/wp-core/wp-config.php` file
permissions to `660`.

### WordPress Plugins

Wordless blocks plugin installation through the environment constant `DISALLOW_FILE_MODS` set as `true`. To maintain
your plugins (and also theme) you shall use Composer. You may check for themes and plugins available at
https://wpackagist.org/.

#### WordPress Core

To download WordPress Core files (everything from a regular WordPress folder but `wp-content`, this one we already
have created inside `wp`) we also use Composer. They are kept into `wp/wp-core`. You may have more information about
how to maintain the WordPress version [here](#change-wordpress-version).

> **IMPORTANT:** This process does not install WordPress completely, just download its core files.

##### Change WordPress version

To change the WordPress version used by your project you must change the version constraint of `roots/wordpress`
package into your project `composer.json` file and run the following command:

```shell
composer update roots/wordpress -W
```

### Wordless Public Entrypoint and Symlinks

WordPress recommends that your server uses it root directory as entrypoint. In NGINX that means your configuration
file should have a root defined with the absolute path to your project root directory. The main problem here is that
when a client access your site over the web it may try to access any directory/file inside your project which may be a
great security issue. To avoid this, WordPress keeps getting updates to block correctly the direct access of
sensitive diretories/files. However, third-party plugins directories/files must also keep getting updates to block
these problems.

As we can see, we must trust that all those directories/files are correctly blocked. Also, as our own code would be
somewhere inside the project root, we too must concern about this security issue. To help with that, Wordless
introduces a `public` entrypoint directory to servers at its root. Only the directories/files inside it may be
accessed directly by a client through the internet.

To keep the necessary directories/files accessible to clients you just need to configure the `public-symlinks` key in
`config/wordless.php` with an array where its keys shall be the URI for client access and its values shall be the
relative path from `public` directory to the respective accessible file/directory.

#### Configuring `public-symlinks`

As said before, to include a symbolic link inside `public` directory you only need to map the relative symbolic link
path inside `public` to a given file/directory relative path also from `public` just like follows:

```php
use Wordless\Application\Commands\GeneratePublicWordpressSymbolicLinks;

return [
    // ...
    GeneratePublicWordpressSymbolicLinks::PUBLIC_SYMLINK_KEY => [
        // ...
        'index.php' => '../wp/index.php',
        // ...
    ],
    // ...
];
```

The code above will create a symbolic link called `index.php` to `public/../wp/index.php` (which means `wp/index.php`)
file. Note that as we made our `public` directory as entrypoint to NGINX server `index.php` becomes a valid URI
(`https://example.test/` **index.php**) to your site.

Let's see another example, this time using a directory as symbolic link target:

```php
use Wordless\Application\Commands\GeneratePublicWordpressSymbolicLinks;

return [
    // ...
    GeneratePublicWordpressSymbolicLinks::PUBLIC_SYMLINK_KEY => [
        // ...
        'wp-content/uploads' => '../wp/wp-content/uploads',
        // ...
    ],
    // ...
];
```

The code above will create a symbolic link called `uploads` to `public/../wp/wp-content/uploads` (which means `wp/index.php`)
file. Note that as we made our `public` directory as entrypoint to NGINX server `index.php` becomes a valid URI
(`https://example.test/` **index.php**) to your site.

#### The `.wlsymlinks` file

To do.

### Caches

To do.

### Database Migrations

To do.

### WordPress Abstractions

As WordPress isn't object-oriented, to help developers we had introduced some classes to manage its abstractions as
follows.

#### Custom Post Types

To do.

#### Schedules

To do.

#### Users Roles Synchronization

To do.

### WordPress Admin Panel

#### Diagnostics widget

This panel maybe annoying users who log in into admin panel with information like "auto updating disabled" or
"missing default theme". Those messages are useful for users that are managing their own site without developers, but
for Wordless case it's just annoying or not important. So you may manage what user roles are able to see this widget
through `config/wordpress.php`, adding or removing user roles slugs from `show_diagnostics_only_to` array key.

## Common Issues

- **Bash error when entering workspace container (Windows)**: Sometimes your Git for Windows may bypass our
  `.gitattributes` configuration for `docker` directory and clone their files with CRLF line endings (Windows style).
  If you build your workspace container with `\r\n` as your line endings the following warning will appear:
  ```shell
  bash: $'\r': command not found
  bash: $'\r': command not found
  bash: /home/laradock/aliases.sh: line 118: syntax error near unexpected token `$'{\r''
  'ash: /home/laradock/aliases.sh: line 118: `function mkd() {
  ```
  To get rid of it change the line endings of all files inside `docker` to LF (Unix Style) and build your containers
  again with `docker compose build --no-cache`.
- **_Slow_ internet connection** on WSL (which makes Docker containers also with limited internet connection):
  https://townsyio.medium.com/wsl2-how-to-fix-download-speed-3edb0c348e29#2b4c.
- **_No_ internet connection** on WSL (which makes Docker containers also disconnected from internet):
  _Based on: https://askubuntu.com/a/1401317_
    1. Open WSL2 terminal;
    2. Create or append file at `/etc/wsl.conf`: `sudo vim /etc/wsl.conf`;
    3. Inside it write the following lines:
       ```
       [network]
       generateResolvConf = false
       ```
    4. Close WSL2 terminal and open a PowerShell terminal;
    5. Run `wsl --shutdown`;
    6. Open WSL2 terminal again;
    7. Remove the old `/etc/resolv.conf`: `sudo rm -rf /etc/resolv.conf`;
    8. Create a new `/etc/resolv.conf`: `sudo vim /etc/resolv.conf`;
    9. Inside it write the following lines:
       ```
       nameserver 8.8.8.8
       nameserver 1.1.1.1
       ```
