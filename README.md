# Wordless

A Headless WordPress Project **for developers** who are tired of WordPress

- [Do It Quick](#do-it-quick)
- [Download](#download-wordless)
    - [Installation](#install-wordless)
- [WordPress Version Update](#wordpress-version-update)
- [WordPress discussion](#about-developers-and-wordpress)
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

## WordPress Version Update

```shell
composer update roots/wordpress -W
```

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

### Directory and files organization

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
create your own commands at `Commands` directory extending `\Wordless\Adapters\WordlessCommand`.

#### Install Wordless

To a fresh start, just run `php console wordless:install` at project's root.

> If you want to check what's happening during this script run it with the `-v` flag: `php console wordless:install -v`.

##### .env

Firstly, Wordless creates a fresh new `.env` file from `.env.example` **if you don't already have one**. Then you shall
be prompted to fill `.env` file values marked as `VALUE_NAME=$VALUE_NAME` (even if the file wasn't created now).

> **IMPORTANT:** if you need to avoid console prompt you may define default values into PHP `$_ENV` super global (in
> whatever way you want). It should be keyed just like `Wordless\Helpers\Environment::COMMONLY_DOT_ENV_DEFAULT_VALUES`.
> Also, you may use the option `--no-ask` which will only try to find values defined into `$_ENV` or leave them as it
> is.

`.env` is a file used to keep values that maybe different on each environment your project is running (for example if
you have a product, local and staging environments to your app). For this reason **IT SHOULD NEVER BE VERSIONED BY GIT**
. You're free to add more values as you need, but keep in mind that those already present into `.env.example`
are core values to Wordless.

You may access the `.env` values through your entire project using `Environment::get()` helper:

```php
use Wordless\Helpers\Environment;
// ...
$admin_mail = Environment::get('WP_ADMIN_EMAIL', 'A default value');
```

> **IMPORTANT:** as you can see `.env` defines the SALT values. They're automatically generated through
> https://api.wordpress.org/secret-key/1.1/salt/

##### WordPress Core

The next installation step is to download WordPress Core files (everything from a regular WordPress folder but
wp-content, this one we already have created inside `public_html/wp-cms`). They are kept into
`public_html/wp-cms/wp-core`. Wordless **won't download** anything if this folder has any other file than `.gitignore`.
This does not install WordPress completely, just download some of its files.

The environment value `WP_VERSION` controls which version shall be downloaded. If it's commented or not present, it
shall get the latest WordPress version to download.

After that Wordless creates the famous `wp-config.php` file from its stubs
(`vendor/thbighead/wordless-framework/src/stubs/wp-config.php`) if it's not present into `public_html/wp-cms/wp-core`.

##### robots.txt

A basic `robots.txt` will be created based on `vendor/thbighead/wordless-framework/src/stubs/robots.txt` if you don't
have one inside `public_html`. You may modify the stub file as you wish and even making references to `.env` values by
surrounding them with brackets (just like we did for the Sitemap value inside it).

##### WordPress Database

With the database credentials filled into `.env`, Wordless is able to check if your server already has the necessary
database to proceed installation. If the database is not present, it shall be created **empty** (without tables) right
now.

##### WordPress Install

There we go. Now Wordless will check your database and if it's not ok it'll install a fresh WordPress database based on
the core files downloaded previously. If the database is already installed, then Wordless will get minor updates to your
WordPress version.

> **IMPORTANT:** at this step we also update `siteurl` value at database and set the site to maintenance mode.

##### .htaccess

As we use NGINX instead of Apache, those files are all ignored.

##### Activating Themes and Plugins

Activates the theme from `WP_THEME` `.env` variable. The default is our blank theme: `wordless`.

Then, activates all plugins.

##### Installing WordPress Languages

Wordless will try to `explode` the `WP_LANGUAGES` through `,` character and install them to plugins and core.

All Languages are installed for plugins, but for WordPress Core only the first one shall be installed, so **be sure to
choose carefully what language will be listed into it**. If `WP_LANGUAGES` is empty this procedure will be skipped.

##### Make WordPress Blog Public

If your `.env` variable `APP_ENV` goes for `production` we set `blog_public` database value to `true`, otherwise we set
it to `false`. After that, we turn off the maintenance mode.

##### WordPress Configuration File Permissions

To avoid any problem when in production environment (`APP_ENV=production`) we set
`public_html/wp-cms/wp-core/wp-config.php` file permissions to `660`.

#### Running WP-CLI commands

By running `php console wp:run "command with arguments and options here"` Wordless shall execute any WP-CLI commands.
Just take care to use the quotes to surround the command, for example:

```
php console wp:run "cache flush"
```

Wordless keeps WP-CLI through Composer and will choose the correct script file to execute according to your Operational
System.

### WordPress Plugins

Wordless blocks plugin installation through the environment constant `DISALLOW_FILE_MODS` set as `true`. To maintain
your plugins (and also theme) you shall use Composer. You may check for themes and plugins available at
https://wpackagist.org/.

#### Must Use Plugins (mu-plugins)

If you need to add some homemade plugins, or maybe you want to install a plugin unavailable from WPackagist, you should
add it to `public_html/wp-cms/wp-content/mu-plugins` folder. After it, you must remount your `wp-load-mu-plugins.php`
to load your new plugins correctly. To achieve this you may run `php console mup:loader` command which will **load
every PHP file inside `mu-plugins` path recursively**.

If your Must-Use Plugin has any kind of entrypoint and should not get all its PHP files loaded in alphabetical order,
you may add them to `config/mu-plugins.php` file. There you must define using the name of plugin directory which files
must be loaded in what order using relative pathing. Example:

```php
return [
    'advanced-custom-fields' => 'acf.php',
    'my-awesome-modification-to-advanced-custom-fields-homemade-plugin' => [
        'this-file-first.php',
        'advanced/something/and-that-file-next.php'
    ],
    '1st-plugin' => '.'
];
```

In the example above, `1st-plugin` PHP scripts will be loaded after `advanced-custom-fields/acf.php`. As we defined a
dot ("."), every PHP script inside `1st-plugin` will be loaded in alphabetical order. The plugin
`my-awesome-modification-to-advanced-custom-fields-homemade-plugin` will load only `this-file-first.php` and
`advanced/something/and-that-file-next.php` in that specific order. Any other PHP files will be ignored. Also,
everything defined in `mu-plugins.json` are loaded after all other PHP files found automatically reading `mu-plugins`
directory.

### WordPress Admin Panel

#### Diagnostics widget

This panel maybe annoying users who log in into admin panel with information like "auto updating disabled" or
"missing default theme". Those messages are useful for users that are managing their own site without developers, but
for Wordless case it's just annoying or not important. So you may manage what user roles are able to see this widget
through `config/admin.php`, adding or removing user roles slugs from `show_diagnostics_only_to` array key.

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
