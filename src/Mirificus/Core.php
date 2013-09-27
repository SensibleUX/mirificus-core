<?php

/**
 * @package Mirificus
 */
namespace Mirificus;

/**
 * This the the glue beween all Mirificus packages.
 * @package Mirificus/Core
 */
class Core {
	/**
	 * The package version.
	 * @var string
	 * @todo Make this dynamic.
	 * @static
	 * @access protected
	 */
	protected static $strVersion = '0.1';

	/**
	 * Holds a globally accessible DB adapter definition.
	 * @var array
	 * @static
	 */
	public static $Database = array();

	/**
	 * Get the version.
	 * @return string The package version.
	 */
	public static function Version(){
		return self::$strVersion;
	}
}