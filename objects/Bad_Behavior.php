<?php

/**
 * Bad Behavior integration class for Core52
 *
 * @author Jonathon Hill <jhill@company52.com>
 */
class Bad_Behavior {
	
	public static $settings = array();
	
	public static function Initialize($log_table = 'bad_behavior', $display_stats = true, $strict = false, $verbose = false, $logging = true, $httpbl_key = '', $httpbl_threat = '25', $httpbl_maxage = '30', $offsite_forms = false) {
		
		if(is_array($log_table)) {
			extract($log_table);
		}
		
		self::$settings = compact(
			'log_table',
			'display_stats',
			'strict',
			'verbose',
			'logging',
			'httpbl_key',
			'httpbl_threat',
			'httpbl_maxage',
			'offsite_forms'
		);
		
		if(!defined('BAD_BEHAVIOR_PRESENT')) {
			define('BAD_BEHAVIOR_PRESENT', TRUE);
		}
		
		self::Install();
		bb2_start(bb2_read_settings());
		
		if(!defined('BAD_BEHAVIOR_ALLOW')) {
			define('BAD_BEHAVIOR_ALLOW', TRUE);
		}
	}
	
	
	/**
	 * Installation
	 */
	function Install() {
		$settings = bb2_read_settings();
		if (!$settings['logging']) return;
		bb2_db_query(bb2_table_structure($settings['log_table']));
	}
	
	
}


define('BB2_CWD', PATH_CORE.'3rdparty/badbehavior');

require_once(BB2_CWD.'/bad-behavior/version.inc.php');
require_once(BB2_CWD.'/bad-behavior/core.inc.php');


/**
 * Return current time in the format preferred by your database.
 */
function bb2_db_date() {
	return gmdate('Y-m-d H:i:s');	// Example is MySQL format
}

/**
 * Return affected rows from most recent query.
 */
function bb2_db_affected_rows() {
	return @mysql_affected_rows(database()->connection());
}

/**
 * Escape a string for database usage
 * @param string $string
 */
function bb2_db_escape($string) {
	return database()->escape($string, database()->get_escape_options() & ~DatabaseConnection::ESCAPE_QUOTE);
}

/**
 * Return the number of rows in a particular query.
 * @param $result
 */
function bb2_db_num_rows($result) {
	return ($result !== FALSE)? count($result) : 0;
}

/**
 * Run a query and return the results, if any.
 * Should return FALSE if an error occurred.
 * Bad Behavior will use the return value here in other callbacks.
 *
 * @param string $query
 * @return array
 */
function bb2_db_query($query) {
	$result = database()->execute($query);
	return ($result->null_set())? FALSE : $result->result_array();
}

/*
 * Return all rows in a particular query.
 * Should contain an array of all rows generated by calling mysql_fetch_assoc()
 * or equivalent and appending the result of each call to an array.
 *
 * @param unknown $result
 * @return unknown
 */
function bb2_db_rows($result) {
	return $result;
}

/**
 * Return emergency contact email address.
 */
function bb2_email() {
	return Config::get('EMAIL_DEBUG_RCPT');
}

/**
 * Retrieve settings from database
 */
function bb2_read_settings() {
	return Bad_Behavior::$settings;
}

/**
 * Save settings
 * @param array $settings
 */
function bb2_write_settings($settings) {
	Bad_Behavior::$settings = array_merge(Bad_Behavior::$settings, $settings);
	return TRUE;
}

/**
 * Screener
 *
 * Insert this into the <head> section of your HTML through a template call
 * or whatever is appropriate. This is optional we'll fall back to cookies
 * if you don't use it.
 *
 */
function bb2_insert_head() {
	global $bb2_timer_total;
	global $bb2_javascript;
	echo "\n<!-- Bad Behavior " . BB2_VERSION . " run time: " . number_format(1000 * $bb2_timer_total, 3) . " ms -->\n";
	echo $bb2_javascript;
}

/**
 * Display stats? This is optional.
 *
 * @param boolean $force
 */
function bb2_insert_stats($force = false) {

}

/**
 * Return the top-level relative path of wherever we are (for cookies)
 * You should provide in $url the top-level URL for your site.
 */
function bb2_relative_path() {
	return '/';
}

