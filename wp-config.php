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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'fu' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

const WP_DEBUG = false;


/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'SIKU/Cp5ztBacdvYCdaQy3NTQ45davH6VFk3kfPC4elTwCCAED1x1ApF4g3AsZaRv2/qKqbm+2i8iVIkC6YUFQ==');
define('SECURE_AUTH_KEY',  'MY2nmUduW0ybKoAzUNdzg8brRCLbqhJWdbXhr6eLIuz5o4I64bpFrmmvD/ASCW05Z+pywEE/zNe36piT7VCStA==');
define('LOGGED_IN_KEY',    'SYbbpep3tL3Hvq9tKbYRqcn2rLBHK8Rf/v5GsfjrqzPvpuH4wDmYlwjwB78208fadcHXyVlzb1rkaURAq7+n6w==');
define('NONCE_KEY',        '5+kaf+Eca5JGPUV8gZFMQvIpnT+Lwu+5jju2j00qJp1vVfttAHktkTf93P5AbAVIJMMPf4KHrSsCuIk7fcIkEQ==');
define('AUTH_SALT',        'yvp7YKtCt3FnA51KhOlQeAZsKsEPY8TEezC7m+y9Bg9yMYs8VV9/osv2EeqNhCTGM6OWnPTd+JIFDIIbuPWhfw==');
define('SECURE_AUTH_SALT', 'toJ0LZxWA/K2TxsBc0qunWfgF0L1TojkSqqHaUo3c9urhB8ZY0gnz6tvkugE9KSXQZ/blEM/deqDfbDipwYBUA==');
define('LOGGED_IN_SALT',   'oq9kj6UHcPJLYXv7BpBjRfz6+SxscAmubaZ0x6asnSZ0gzoFOr9fsw0j2KuJzRiL7TdK3N/o4VznR7/GLaSUfQ==');
define('NONCE_SALT',       'SFJ/jYyqJmH8A5r4lmQGjhOuLKoGF9OyMXzhkrX+eS5LGuFhp16qKNjdt6HZyq0UZcL+U63sj3Foq4DF/XyfZA==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
