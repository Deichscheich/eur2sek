<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

define( 'WP_MEMORY_LIMIT', '256M' );

define('WP_HOME','https://www.wasjournalistenwollen.de');
define('WP_SITEURL','https://www.wasjournalistenwollen.de');

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
define( 'DB_NAME', 'd039284b' );

/** Database username */
define( 'DB_USER', 'd039284b' );

/** Database password */
define( 'DB_PASSWORD', 'zAQs7L3ErshvRDGN' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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


define('AUTH_KEY',         'DV)D(%KYVqqcy`6-wS[@-X*h:{eEzMbw%=7,tTp_3Jbkb,F>j`#DG^u$T-CE[$o(');
define('SECURE_AUTH_KEY',  '4t3si=jJ=jQ;Q/eUAl,hs_sJOS@S]o-vqnKr/|B+m s=JoJ0]X+8251sh@-|ZHF+');
define('LOGGED_IN_KEY',    'Ev2@?WLoL_I1IRXT;O/|%XfWq=_N}~eeSlRj8n_i-q+ ?p9_}t;RbL0)FH52#aM:');
define('NONCE_KEY',        'J+u7a_.CQnH<e|eM}U9oq>j6%u@ZMB1..,o$+P!rlamr>[ Lf>uPecjidLh`&g^x');
define('AUTH_SALT',        ':5ZvkK;6Ng@a]]&Wk5`}RmR8S9|aIoE3$pPj1%h&+{7,f%VjJ9;Ec4$PNA.*PvEz');
define('SECURE_AUTH_SALT', '-Kq=|^SXh>_Mh(.ah`74Gh[#4;Yo#@l4*R:5r`YG;rC2>6n/A4g#hwB%%&h+F_P!');
define('LOGGED_IN_SALT',   'MMf`gc52C!L$gm ){kS0[VNG3^(Cx7qjxL21&I6V{7{kN.kk_:Z^u2h7W~dSA9^|');
define('NONCE_SALT',       ':;Movc>c(@okIbGI 7KNQTNLLgmB,=vE@Jt#dPuZv-[0J&9s]7-SX-O7{w[E(|A*');
/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'KzNV5_';
define( 'WP_POST_REVISIONS', 10 );




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

// Enable WP_DEBUG mode
// define( 'WP_DEBUG', true );

// Enable Debug logging to the /wp-content/debug.log file
// define( 'WP_DEBUG_LOG', true );

// Disable display of errors and warnings
// define( 'WP_DEBUG_DISPLAY', false );
// @ini_set( 'display_errors', 0 );

/* Add any custom values between this line and the "stop editing" line. */
define ('WP_MEMORY_LIMIT', '256M');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
