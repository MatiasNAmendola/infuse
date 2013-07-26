<?php

/**
 * @package Infuse
 * @author Jared King <j@jaredtking.com>
 * @link http://jaredtking.com
 * @version 1.0
 * @copyright 2013 Jared King
 * @license MIT
	Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
	associated documentation files (the "Software"), to deal in the Software without restriction,
	including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
	and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
	subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in all copies or
	substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
	LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
	IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
	SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace infuse\libs;

use \infuse\Config;
use \infuse\Database;

class SiteStats
{
	static $historyMetrics = array(
		'database.size',
		'database.numTables',
		'users.numUsers',
		'users.numGroups',
		'users.dailySignups'
	);

	/**
	 * Generates a snapshot from the current stats
	 *
	 * @return array
	 */
	static function generateSnapshot()
	{
		$return = array();
		
		/* PHP Statistics */
		$return['php'] = array();
		
		// php version
		$return['php']['version'] = phpversion();
		
		/* Site Statistics */
		$return['site'] = array();
		
		// site status
		$return['site']['status'] = !Config::value( 'site', 'disabled' );

		// site mode
		$return['site']['mode'] = Config::value( 'site', 'production-level' );
		
		// session adapter
		$return['site']['session'] = Config::value( 'session', 'adapter' );		

		/* Infuse Statistics */
		$return[ 'infuse' ] = array();
		
		// load composer.json
		$infuseComposer = json_decode( file_get_contents( INFUSE_BASE_DIR . '/composer.json' ) );
		
		// infuse version
		$return['infuse']['version'] = $infuseComposer->version;
		
		/* Database Statistics */
		$return['database'] = array();

		// DB Type
		$query = Database::sql( 'SELECT VERSION()' );
		$return['database']['version'] = $query->fetchColumn( 0 );		
		
		// Get table information.
		$query = Database::sql("SHOW table STATUS");
		
		$status = $query->fetchAll( \PDO::FETCH_ASSOC );
		
		$dbsize = 0;
		// Calculate DB size by adding table size + index size:
		foreach( $status as $row )
			$dbsize += $row['Data_length'] + $row['Index_length'];
			
		$return['database']['size'] = $dbsize;
		
		// number of tables
		$return['database']['numTables'] = count( $status );
		
		/* User Statistics */
		$return['users'] = array();
		
		// total number of users
		$return['users']['numUsers'] = (int)Database::select(
			'Users',
			'count(*)',
			array(
				'single' => true ) );

		// number of groups
		$return['users']['numGroups'] = (int)Database::select(
			'Groups',
			'count(*)',
			array(
				'single' => true ) );
						
		// newest user
		$return['users']['newestUser'] = Database::select(
			'Users',
			'uid',
			array(
				'orderBy' => 'registered_on DESC',
				'single' => true ) );

		// daily signups
		$return['users']['dailySignups'] = (int)Database::select(
			'Users',
			'count(uid)',
			array(
				'where' => array(
					'registered_on > ' . strtotime('today') ),
				'single' => true ) );

		return $return;
	}
	
	/**
	 * Gets the latest stats snapshot
	 *
	 * @return array
	 */
	static function getLatestSnapshot()
	{
		return json_decode( Database::select(
			'Site_Stats_History',
			'stats',
			array(
				'orderBy' => 'timestamp DESC',
				'single' => true,
				'limit' => '0,1'
		)), true);
	}
	
	/**
	 * Gets the history for a given metric
	 *
	 * @param string $metric
	 * @param string $start
	 * @param string $end
	 *
	 * @return false|array
	 */
	static function history( $metric, $start, $end )
	{
		if( !in_array( $metric, self::$historyMetrics ) )
			return false;
			
		$stats = Database::select(
			'Site_Stats_History',
			'stats,timestamp',
			array(
				'where' => array(
					"`timestamp` >= '$start'",
					"`timestamp` <= '$end'" ),
				'orderBy' => 'timestamp ASC' ) );
		
		$series = array();
		
		foreach( $stats as $day )
		{
			$decoded = json_decode( $day[ 'stats' ], true );
			
			$series[ date( 'm/d/Y', $day[ 'timestamp' ] ) ] = Util::array_value( $decoded, $metric );
		}
		
		return $series;
	}
	
	/**
	 * Captures a screenshot of the current stats
	 *
	 * @return boolean
	 */
	static function captureSnapshot()
	{
		// generate a snapshot
		$snapshot = self::generateSnapshot();
		
		// only save the history metrics
		$stats = array();
		
		foreach( self::$historyMetrics as $metric )
			Util::array_set( $stats, $metric, Util::array_value( $snapshot, $metric ) );
		
		// save it in the DB
		return Database::insert(
			'Site_Stats_History',
			array(
				'timestamp' => time(),
				'stats' => json_encode( $stats )
			)
		);
	}
}