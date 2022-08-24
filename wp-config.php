<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)
    $_SERVER['HTTPS']='on';

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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'blog' );

/** MySQL database username */
define( 'DB_USER', 'blog' );

/** MySQL database password */
define( 'DB_PASSWORD', 'SxPpr5zQ5CH98RGc' );

/** MySQL hostname */
define( 'DB_HOST', 'meutudo-blog-wp-5.cux1lua07njc.sa-east-1.rds.amazonaws.com' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'wHEb_LY.`Mk5d~Bk832E.b%v5+q.A6=M*!lQVe8Eqt<z~YX9ZlB23}1Swj[I/AVe' );
define( 'SECURE_AUTH_KEY',  'm(L>xs?pT Sn6w}GD.^5`|3#$6Wa7Wt)nbP]r#~4Xb5<&;~GCZn?@;AOVx&;eDK_' );
define( 'LOGGED_IN_KEY',    'A.S$?WL[w|cKY}un$dhbs!${nBT4I(_M,hafpJs:nb4y{9hOMcMNVn38ct9+r6}<' );
define( 'NONCE_KEY',        'ivCCb5<S:1F>O}gf!}kAb<e@/|imgG *QUbte2KVdFJSqQfn+&MEc@Ee?/iPzbT?' );
define( 'AUTH_SALT',        '~{%<aTQq/F6%5*e@hA^lsefyt+~0?F?digyb+gHGf[02QSkM2>OJYyklD(Mohp6n' );
define( 'SECURE_AUTH_SALT', 'l=}i(@9P@]nv|+!+Mo%/@[BztvB>vb@7@l+9WCWg@Q=+ME52Mj?9oS,X9&0GO9S)' );
define( 'LOGGED_IN_SALT',   '#ll:i$(C(owt<U62;0_r_;@Ojds)3{,nKN5GGMlq^M7EYbC!|3onw5@j&i9e~y?c' );
define( 'NONCE_SALT',       '|XXYwba@Jr!O1plX,W(JMwpyA9UdIltF;OnM qrLSXW4[hsq<>z*=JZ0&Al*JJDW' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
