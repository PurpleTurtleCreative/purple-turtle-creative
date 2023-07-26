<?php
/**
 * Util Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

/**
 * Static class of generic utility functions.
 */
class Util {

	/**
	 * Gets the SQL DateTime string of a Unix timestamp.
	 *
	 * @param int|null $unix_timestamp Optional. The number of
	 * seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
	 * Defaults to the current Unix timestamp.
	 *
	 * @return string The SQL DateTime timestamp string.
	 */
	public static function unix_as_sql_timestamp(
		?int $unix_timestamp = null
	) : string {
		if ( null === $unix_timestamp ) {
			$unix_timestamp = time();
		}
		return gmdate( 'Y-m-d H:i:s', $unix_timestamp );
	}

	/**
	 * Gets the Unix timestamp of a SQL DateTime string.
	 *
	 * @param string $sql_timestamp The SQL DateTime timestamp
	 * string.
	 *
	 * @return int The SQL DateTime timestamp string.
	 */
	public static function sql_timestamp_as_unix( string $sql_timestamp ) : int {
		return \DateTimeImmutable::createFromFormat(
			'Y-m-d H:i:s',
			$sql_timestamp,
			new \DateTimeZone( 'UTC' )
		)->getTimestamp();
	}

	/**
	 * Checks if a SQL DateTime string is more than a given
	 * duration in the past.
	 *
	 * @param string $sql_timestamp The SQL DateTime string.
	 * @param int $ttl_seconds The duration in seconds.
	 *
	 * @return bool
	 */
	public static function is_sql_timestamp_expired(
		string $sql_timestamp,
		int $ttl_seconds
	) : bool {
		return (
			time() - static::sql_timestamp_as_unix( $sql_timestamp ) >= $ttl_seconds
		);
	}

	/**
	 * Checks if a Unix seconds timestamp is more than a given
	 * duration in the past.
	 *
	 * @param int $unix_timestamp The Unix seconds timestamp.
	 * @param int $ttl_seconds The duration in seconds.
	 *
	 * @return bool
	 */
	public static function is_unix_expired(
		int $unix_timestamp,
		int $ttl_seconds
	) : bool {
		return ( time() - $unix_timestamp >= $ttl_seconds );
	}
}
