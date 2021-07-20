<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

/**
 * Customized wp-config.php. DO NOT CHANGE THIS COMMENT!
 * @author Wordless
 */
require_once __DIR__ . '/../../../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Wordless\Helpers\Environment;
use Wordless\Helpers\Str;

(new Dotenv())->load(__DIR__ . '/../../../.env');

// https://wordpress.org/support/article/editing-wp-config-php/#require-ssl-for-admin-and-logins
const DISALLOW_FILE_MODS = true;
// https://wordpress.org/support/article/editing-wp-config-php/#disable-wordpress-auto-updates
const AUTOMATIC_UPDATER_DISABLED = true;
// https://wordpress.org/support/article/editing-wp-config-php/#disable-wordpress-core-updates
const WP_AUTO_UPDATE_CORE = false;

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

define('DB_NAME', Environment::get('DB_NAME'));

/** MySQL database username */
define('DB_USER', Environment::get('DB_USER'));

/** MySQL database password */
define('DB_PASSWORD', Environment::get('DB_PASSWORD'));

/** MySQL hostname */
define('DB_HOST', Environment::get('DB_HOST'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', Environment::get('DB_CHARSET', 'utf8'));

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', Environment::get('DB_COLLATE', 'utf8_general_ci'));

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', Environment::get('AUTH_KEY'));
define('SECURE_AUTH_KEY', Environment::get('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', Environment::get('LOGGED_IN_KEY'));
define('NONCE_KEY', Environment::get('NONCE_KEY'));
define('AUTH_SALT', Environment::get('AUTH_SALT'));
define('SECURE_AUTH_SALT', Environment::get('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', Environment::get('LOGGED_IN_SALT'));
define('NONCE_SALT', Environment::get('NONCE_SALT'));

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = Environment::get('DB_TABLE_PREFIX', 'wp_');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', $debug = Environment::get('WP_DEBUG', false));
// https://wordpress.org/support/article/editing-wp-config-php/#configure-error-logging
define('WP_DEBUG_LOG', $debug);
define('WP_DISABLE_FATAL_ERROR_HANDLER', $debug);

// https://wordpress.org/support/article/editing-wp-config-php/#disable-wordpress-auto-updates
define('COOKIE_DOMAIN', Str::after($site_url = Environment::get('APP_URL'), '://'));

// https://wordpress.org/support/article/editing-wp-config-php/#blog-address-url
define('WP_HOME', $site_url);

// https://wordpress.org/support/article/editing-wp-config-php/#wp_siteurl
$site_url = Str::finishWith($site_url, '/');
define('WP_SITEURL', "{$site_url}wp-cms/wp-core");

// https://wordpress.org/support/article/editing-wp-config-php/#moving-wp-content-folder
define('WP_CONTENT_DIR', realpath(__DIR__ . '/../wp-content'));
define('WP_CONTENT_URL', "{$site_url}wp-content");

// https://wordpress.org/support/article/editing-wp-config-php/#wp_environment_type
define('WP_ENVIRONMENT_TYPE', $environment = Environment::get('APP_ENV', Environment::LOCAL));

// https://wordpress.org/support/article/editing-wp-config-php/#require-ssl-for-admin-and-logins
define('FORCE_SSL_ADMIN', $environment === Environment::PRODUCTION);

// https://wordpress.org/support/article/editing-wp-config-php/#block-external-url-requests
const WP_HTTP_BLOCK_EXTERNAL = true;
$additional_allowed_hosts = Environment::get('WP_ACCESSIBLE_HOSTS', '*.wordpress.org');
if (!empty($additional_allowed_hosts)) define('WP_ACCESSIBLE_HOSTS', $additional_allowed_hosts);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
