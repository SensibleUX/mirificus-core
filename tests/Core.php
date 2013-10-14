<?php

use Mirificus\Core;

class CoreTest extends PHPUnit_Framework_TestCase
{
	public function testVersion()
	{
		$version = Core::Version();
		$this->assertTrue(isset($version));
	}
}