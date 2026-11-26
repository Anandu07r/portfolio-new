<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'portfolio-new' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'U4gvLipu~ 06-6**qHcd6/`Y~ud[#abd8m^MI*UVL~]nC>un4Kt*g!h1hc)vT:e7' );
define( 'SECURE_AUTH_KEY',  '.nPOg~1dI*T*z<7A~0h+?h%gOc?1>r,X83g:}ifPlri]!; N%IvJ7`)^Ievdhr~W' );
define( 'LOGGED_IN_KEY',    'g(J~[:GaB#un=s[- x}mo-L4,d&Aj2)M~Hefp7{lyal!$X@:o.crtvFgq4!a7(Cu' );
define( 'NONCE_KEY',        '9 2JPD1+mtt!?>*SCf%-2hCCBBO?W.>PSMrs@qKn0&66[NwW*995@-[d~h)V8Bsv' );
define( 'AUTH_SALT',        '}[vH50Wcjf6x=`80,#wvV>.?drL<QS_lBy>0SLaO*RU`=#}-=<t=1Q@ c|ek)@tE' );
define( 'SECURE_AUTH_SALT', 'ueHMEcceG+nY7!1JnVE-V[8?Bh:n[HqS@y<,_#-7Xv]Yq7i:,]U(*F<%${bnX4z+' );
define( 'LOGGED_IN_SALT',   '42RWm^BOqWmtrA3I[3ork.:~Gpv@D#^}C>}CN`aZ@+Bu:Z5b?@!85>&mC%<;*Z|U' );
define( 'NONCE_SALT',       'lU9bbeXY-327BO[Yo}Zj][&!/afu4WiqzSUWs3Sr{8g1KCu*s`Xt9lARqRX&C47-' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
