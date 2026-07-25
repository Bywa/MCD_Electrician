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
define( 'DB_NAME', 'ecoelectricite' );

/** Database username */
define( 'DB_USER', 'mysql_eco@bywacreations.com' );

/** Database password */
define( 'DB_PASSWORD', 'ECOElec2026!' );

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
define('AUTH_KEY',         'h`Rvc}~aBIpI-vo8j%(.#OMD?VmP>14?sBt5bU27ZDi`h=Bm$wk-srPggn??k.T`');
define('SECURE_AUTH_KEY',  '|mbE!;j``I_N_f.7MDl{,A=?^TI}YGG. .,`W1=gM)87XYU7 h5X%zo_z%+DFMwX');
define('LOGGED_IN_KEY',    'r;K1sK;+z)A-i{6:yUrT^Qi~he4r*VWs1<3NT-=8b:YgBS7U$3%(NUG#$Q+Cz1A}');
define('NONCE_KEY',        'k7(U@WF05%2Lf~kf^}WN!FxcTnw O|s-s==4/w?4EZnV7+?RK]<QWQ_AnDsp8-WF');
define('AUTH_SALT',        'bpjUhK?~GP`++-MPjUW<LLHUE030@M>p.-k+/tC<T7;BDi.LCSm <NWlx++rmWbB');
define('SECURE_AUTH_SALT', 'K+TY%R:^Lj}YbWj06Og0+zvnNLrFM^c_}Pu|[2Z#}|ik:Kc7rfAa(*%aaq^JXnKj');
define('LOGGED_IN_SALT',   'a*G `GX96[<(?p/[G_tuQ>qJj}V^B5P~,GQ@-)EVs>%V@|Vy_rK0Eq8_ok2e5&h]');
define('NONCE_SALT',       'c9Hz{KL>mG5Ex-IL*11%YL|P=O^FtUO}0#rTDo7,DW*-V18&#Q%|tV9DyGl/JM}-');


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
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', true); // IMPORTANT → pas de cache JS/CSS

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
