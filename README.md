# Wordless

A Headless WordPress Project **for developers** who are tired of WordPress

- [Download](#download-wordless)
    - [Installation](#install-wordless)
- [WordPress discussion](#about-developers-and-wordpress)
- [Wordless Project Directories](#directory-and-files-organization)
- [Wordless CLI](#wordless-cli)
    - [Running WP-CLI](#running-wp-cli-commands)

## Download Wordless

Just run the following code:

```
composer create-project thbighead/wordless example-app

cd example-app
```

There you go. Inside this new folder you may install the project using [`wordless:install`](#install-wordless) command.

## About developers and WordPress

> *If you're a WordPress fan make sure you understand WordPress **isn't** any kind of tool made for developers.
> We'll discuss it further bellow.*

> *If you're a PHP developer make sure you understand WordPress **is** old and still lives by getting massive
> updates frequently. You don't should be afraid about old code, you can nail then: you just need your time.*

WordPress is a powerful [CMS](https://en.wikipedia.org/wiki/Content_management_system) which grew up for many years and
nowadays can give any non-IT person an opportunity to fully create and customize a blog-like website. But more than just
maintain a content, the admin panel makes you powerful to even change the whole page code. There is where the magic, yet
the problem, starts.

WordPress is made upon plugins and themes which don't need to follow any kind of project structure. All you need to do
is take an adventure through WordPress hooks, sometimes called filters and/or actions and do what you want to do.
Although WordPress cares to introduce some concepts to organize and name theme files like excerpt, post, pages,
categories, taxonomies, etc., each theme have a completely different directory organization and project structure.
Finally, when talking about plugins project organization goes even more complicated, and we realize that even WordPress
is being constantly updated its code still lacks some refactoring.

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
theme and use it to serve your content code through the API. *(which we really do not recommend, if that is your
solution, just get a normal WordPress installation and be happy)*

### Directory and files organization

```
| public_html (Websystem entrypoint)
| \
|  | wp-cms
|  | \
|  |  | wp-content
|  |  | \
|  |  |  | languages (Just like WordPress. Everything inside is ignored by Git)
|  |  |  | mu-plugins (Just like WordPress. Place any handmade or modified plugin here)
|  |  |  | plugins (Just like WordPress. Everything inside is ignored by Git. Installation controlled by Composer)
|  |  |  | themes (Just like WordPress. Ships with wordless theme)
|  |  |  | uploads (For built-in WordPress filesystem. Everything inside is ignored by Git)
|  |  |  |>debug.log (WordPress log file)
|  |  | wp-core (WordPress core files (anything but wp-content))
|  |>.htaccess (Customized against Pingback Exploit and more)
|  |>index.php (Just like WordPress)
|  |>robots.txt (auto-generated after wordless:install)
| src
| \
|  | Adapters (Keep all classes adapted from vendor classes)
|  | Commands (Wordless CLI commands. Make new as you wish, customize carefully)
|  | Exception (Keep custom Exceptions. Better then if-else)
|  | Helpers (Keep helper classes)
|  | stubs (Keep files stubs to generate new ones)
|>.env.example (Used to create new .env files)
|>composer.json (Composer)
|>console (Wordless CLI file)
|>wp-cli.yml (WP-CLI config file)
```

### Wordless CLI

The `console` has many commands to help you install and keep your project running. To check what commands are available,
just run `php console list`. To have more info about each command run `php console {command:alias} --help`.

#### Install Wordless

To a fresh start, just run `php console wordless:install` at project's root.

##### .env

Firstly, Wordless creates a fresh new `.env` file from `.env.example` if you don't already have one. Then you shall be
prompted to fill `.env` file values marked as `VALUE_NAME=$VALUE_NAME` (even if the file wasn't created now).

> **IMPORTANT:** if you need to avoid console prompt you may define default values into PHP `$_ENV` superglobal (in
> whatever way you want) or `Wordless\Helpers\Environment::COMMONLY_DOT_ENV_DEFAULT_VALUES` constant (`$_ENV` takes
> priority and should be keyed just like for this constant). Also, you may use the option `--no-ask` which will only
> try to find values defined into `$_ENV` or leave them as it is.

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

After that Wordless creates the famous `wp-config.php` file from its stubs (`src/stubs/wp-config.php`) if it's not
present into `public_html/wp-cms/wp-core`.

##### robots.txt

A basic `robots.txt` will be created based on `src/stubs/robots.txt` if you don't have one inside `public_html`. You may
modify the stub file as you wish and even making references to `.env` values by surrounding them with brackets
(just like we did for the Sitemap value inside of it).

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

To correctly use WordPress REST API we must define the permalink to Post names. So now we do fix `.htaccess` and
database to ensure it.

##### Activating Themes and Plugins

Activates the theme from `WP_THEME` `.env` variable. The default is our blank theme: `wordless`.

Then, activates all plugins.

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
