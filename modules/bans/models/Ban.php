<?php
/**
 * This class represents a ban. Bans may be on IP addresses, e-mail addressses, or user name
 *
 * @package nFuse
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
 
namespace nfuse\models;

use \nfuse\Database as Database;

define( 'BAN_TYPE_IP', 1 );
define( 'BAN_TYPE_USERNAME', 2 );
define( 'BAN_TYPE_EMAIL', 3 );

class Ban extends \nfuse\Model
{
	protected static $tablename = 'Ban';
	public static $properties = array(
		array(
			'title' => 'ID',
			'name' => 'id',
			'type' => 2
		),	
		array(
			'title' => 'Type',
			'name' => 'type',
			'type' => 5,
			'enum' => array (
				1 => 'IP',
				2 => 'Username',
				3 => 'E-mail Address' )
		),	
		array(
			'title' => 'Value',
			'name' => 'value',
			'type' => 2
		),	
		array(
			'title' => 'Reason',
			'name' => 'reason',
			'type' => 2
		)
	);
	
	
	/**
	 * Checks if a value has been banned.
	 *
	 * @param string $value value
	 * @param int $type type
	 *
	 * @return boolean
	 */
	static function isBanned( $value, $type )
	{
		if( !in_array( $type, array( BAN_TYPE_IP, BAN_TYPE_USERNAME, BAN_TYPE_EMAIL ) ) )
			return false;
		
		return Database::select(
			'Ban',
			'count(*)',
			array(
				'where' => array(
					'type' => $type,
					'value' => $value ),
				'single' => true ) ) > 0;
	}
}