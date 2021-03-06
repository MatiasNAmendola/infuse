<?php

/**
 * @package infuse\framework
 * @author Jared King <j@jaredtking.com>
 * @link http://jaredtking.com
 * @version 0.1.15.4
 * @copyright 2013 Jared King
 * @license MIT
 */

namespace infuse\controllers;

class Home extends \infuse\Controller
{
	public static $properties = array(
		'title' => 'Home',
		'description' => 'Skeleton module for adding routes and views.',
		'version' => '1.0',
		'author' => array(
			'name' => 'Jared King',
			'email' => 'j@jaredtking.com',
			'website' => 'http://jaredtking.com'
		)
	);

	function index( $req, $res )
	{
		$res->render( 'home', array(
			'title' => 'Welcome to Infuse Framework',
			'metaDescription' => 'Infuse Framework allows rapid creation of web applications and APIs.'
		) );
	}
}