<?php
define( 'WP_CACHE', true );
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'interlook' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );
define( 'DB_PORT', '8889' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         's7qycvnoggq222mp8seecdqprqtzmf93fnbjybjn5hkflrt03jumaoeebji0mp76' );
define( 'SECURE_AUTH_KEY',  '0dxw7pgyk5yqloxsibij8zudn1c0bswthzvishwgqpunfhjkoazlolzoo4cmjuft' );
define( 'LOGGED_IN_KEY',    'ubwnz7ln30nvwzosordyerorj1dhk4oaznejzdwcbwuxqcbk8c3jpg3wxtdl5rb3' );
define( 'NONCE_KEY',        'kdlzgwkaygmuv2uxj16st7sne63adza3k7g55ertyoqpybnrliwjiqnndy2yf77p' );
define( 'AUTH_SALT',        'qvxaueobmf4zrpkeihhep62xxl94fyaldul2flzovac44awn4hejntpxi7kdybxc' );
define( 'SECURE_AUTH_SALT', 'avp8fdyqowffkjdbznplnys0ftk5juzlxu6zqsoiydrxngcygptwawex03l03csv' );
define( 'LOGGED_IN_SALT',   'tuowjatr5miyi0qtk6wsuacsiifbi2xnbggqjl7sd4y3xar8l3ktc8adzpqjhmzd' );
define( 'NONCE_SALT',       '6ajoznpckmmurygtuodesz0i8juujsbxhqxcpczdyxkzitvzg7lukiclxsgalqoc' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp0k_';

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
define( 'WP_DEBUG', false );
define( 'WP_MEMORY_LIMIT', '256M' );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
