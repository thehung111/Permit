<?php

use Nth\Permit\Helper\ConfigHelper;
use Mockery as m;
use Illuminate\Support\Facades\Config;

class ConfigHelperTest extends PHPUnit_Framework_TestCase {
	
	public function tearDown()
    {
        m::close();
    }

    // clear the prefix before testing, otherwise subsequent methods will retrieve previous method value
    public function setUp()
    {
    	ConfigHelper::setDBPrefix(null); // clear prefix
    }

	public function testGetDBPrefix()
	{
		Config::shouldReceive('get')->once()->andReturn('nth');
		$db_prefix = ConfigHelper::getDBPrefix();

		$this->assertSame('nth', $db_prefix ); 
	}


	public function testGetDBTableNameWithPrefix()
	{
		Config::shouldReceive('get')->once()->andReturn('n');

		$table_name = ConfigHelper::getDBTableName('test');

		$this->assertSame('n_test', $table_name ); 
	}

	public function testGetDBTableNameWithoutPrefix()
	{
		Config::shouldReceive('get')->once()->andReturn('');

		$table_name = ConfigHelper::getDBTableName('test');

		$this->assertSame('test', $table_name ); 
	}
}