<?php

namespace Mirificus;

class Core {
	protected static $strVersion = '0.1';
	public static $Database = array();

	public static function Version(){
		return self::$strVersion;
	}
}