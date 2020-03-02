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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dss_itsquim' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'vy=s;dqt8bf<W(4Vl{ T#UHXdauR)(Aaz*,>b#~f8-)v;SB):KnPRH2,Y.Q9 |Yx' );
define( 'SECURE_AUTH_KEY',  'sS;Vl{@E}<v15;VCK76d8d|d>ribP,3@.rKg:<4I0fjNnf#C]3MCV)O<:8td<a n' );
define( 'LOGGED_IN_KEY',    'aWNJ4<1^,<OE(*I1TMaiN iA6F9!fRRL$q9Rq3m!cb/f;JOSpuc%f#2#!Q%:.]?k' );
define( 'NONCE_KEY',        'dakgbF!{,pljc>xfq (kZel>S^f=cDjZjA<l^-+`:B!#fu^nqWL[}Qz%[YMOh~E|' );
define( 'AUTH_SALT',        'orQ<o%^M)vQ.N!3[`fb0[Sh(l*@+II}=1j4~V76+%hw2e]212{z<e!{ly*%+E[4t' );
define( 'SECURE_AUTH_SALT', 'aG[PZar:7-G2i3K%O9xkcrNQF.IX+G?X7Z)gU|$qdPZOF#T>Mn{NN<gO[L*YJyJw' );
define( 'LOGGED_IN_SALT',   'U4RJ67[I~D%l?.Z(tAk6,v9N{2Eg>vEE<1F<5Ad3@70a?JV?ls[zGl*+9Oj3WEFA' );
define( 'NONCE_SALT',       'VusD+Dj4v`[_/46%F$7twbVz-VgSXSb3!R[=S~:Op1T$#b:UVg>o?-kvw0w 7z8H' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
