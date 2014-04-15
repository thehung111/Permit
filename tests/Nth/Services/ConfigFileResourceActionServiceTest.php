<?php

use Nth\Permit\Helper\ConfigHelper;
use Mockery as m;
use Illuminate\Support\Facades\Config;
use Nth\Permit\Services\ConfigFileResourceActionService;

class ConfigFileResourceActionServiceTest extends PHPUnit_Framework_TestCase
{
	protected $perms ;

	public function tearDown()
    {
        m::close();
    }

    // clear the prefix before testing, otherwise subsequent methods will retrieve previous method value
    public function setUp()
    {
    	ConfigHelper::setPermResourcesMap(array()); // clear permission setting

    	$this->perms = array( 
			'user' => array(
				array('actionName' => 'user.add'		, 'bitwiseValue' => 1),
				array('actionName' => 'user.edit'		, 'bitwiseValue' => 2),
				array('actionName' => 'user.delete'		, 'bitwiseValue' => 4),
				array('actionName' => 'user.view'		, 'bitwiseValue' => 8),
			),

			'blog_post' => array(
				array('actionName' => 'post.add'		, 'bitwiseValue' => 1),
				array('actionName' => 'post.edit'		, 'bitwiseValue' => 2),
				array('actionName' => 'post.delete'		, 'bitwiseValue' => 4),
				array('actionName' => 'post.view'		, 'bitwiseValue' => 8),
			),
			
		);
	}


    /**
	* @expectedException \InvalidArgumentException
	*/
    public function testcheckActionExists_InvalidArgument()
	{
		
		//Config::shouldReceive('get')->once()->andReturn($this->perms);
		
		$impl = new ConfigFileResourceActionService();

		$resourceName = null ;
		$actionName = '' ;
		$impl->checkActionExists($resourceName, $actionName);
	}

	/**
	* @expectedException \Nth\Permit\Exceptions\NoSuchResourceActionException
	*/
    public function testcheckActionExists_InvalidAction()
	{
		
		Config::shouldReceive('get')->once()->andReturn($this->perms);
		
		$impl = new ConfigFileResourceActionService();

		$resourceName = 'user' ;
		$actionName = 'user.sleep' ;
		$impl->checkActionExists($resourceName, $actionName);
	}

    public function testcheckActionExists_ValidAction()
	{
		
		Config::shouldReceive('get')->once()->andReturn($this->perms);
		
		$impl = new ConfigFileResourceActionService();

		$resourceName = 'user' ;
		$actionName = 'user.add' ;
		$impl->checkActionExists($resourceName, $actionName);
	}

	public function testGetSupportedActions()
	{
		
		Config::shouldReceive('get')->once()->andReturn($this->perms);
		
		$impl = new ConfigFileResourceActionService();

		$resourceName = 'user' ;
		$arr = $impl->getSupportedActions($resourceName);

		$result_arr = array(
				array('actionName' => 'user.add'		, 'bitwiseValue' => 1),
				array('actionName' => 'user.edit'		, 'bitwiseValue' => 2),
				array('actionName' => 'user.delete'		, 'bitwiseValue' => 4),
				array('actionName' => 'user.view'		, 'bitwiseValue' => 8),
			);

		// check that these 2 arrays are the same
		$this->assertEmpty(array_merge(array_diff($arr, $result_arr), array_diff($result_arr, $arr)));
	}

	public function testHasPerm_True()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue = bindec("00110011");
		$actionBitwiseValue  = bindec("00100010");

		$this->assertTrue( $impl->hasPerm($actionsBitwiseValue, $actionBitwiseValue) );

	}

	public function testHasPerm_False()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue = bindec("00110011");
		$actionBitwiseValue  = bindec("01001000");

		$this->assertFalse( $impl->hasPerm($actionsBitwiseValue, $actionBitwiseValue) );

	}

	public function testHasPerms_True()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue    = bindec("00110011");
		$actionBitwiseValueArr  = array( bindec("00000010"), bindec("00100000")  );

		$this->assertTrue( $impl->hasPerms($actionsBitwiseValue, $actionBitwiseValueArr) );

	}

	public function testHasPerms_False()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue = bindec("00110011");
		$actionBitwiseValueArr  = array( bindec("00000010"), bindec("10100000")  );


		$this->assertFalse( $impl->hasPerms($actionsBitwiseValue, $actionBitwiseValueArr) );

	}

	public function testAddPerm()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue    = bindec("00110011");
		$actionBitwiseValue     = bindec("11011101");
		

		$this->assertSame( bindec("11111111") , $impl->addPerm($actionsBitwiseValue, $actionBitwiseValue) );

	}

	public function testAddPerms()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue    = bindec("00110011");
		$actionBitwiseValueArr  = array( bindec("00000010"), bindec("10100100")  );

		$this->assertSame( bindec("10110111") , $impl->addPerms($actionsBitwiseValue, $actionBitwiseValueArr) );

		
	}

	public function testAddPerms_EmptyArr()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue    = bindec("00110011");
		$actionBitwiseValueArr  = array();

		$this->assertSame( bindec("00110011") , $impl->addPerms($actionsBitwiseValue, $actionBitwiseValueArr) );

		
	}

	public function testRemovePerm()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue    = bindec("00110011");
		$actionBitwiseValue     = bindec("11011101");
		

		$this->assertSame( bindec("00100010") , $impl->removePerm($actionsBitwiseValue, $actionBitwiseValue) );

	}

	public function testRemovePerms()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue    = bindec("00110011");
		$actionBitwiseValueArr  = array( bindec("00000010"), bindec("10100100")  );

		$this->assertSame( bindec("00010001") , $impl->removePerms($actionsBitwiseValue, $actionBitwiseValueArr) );

		
	}

	public function testRemovePerms_EmptyArr()
	{
		$impl = new ConfigFileResourceActionService();
		$actionsBitwiseValue    = bindec("00110011");
		$actionBitwiseValueArr  = array();

		$this->assertSame( bindec("00110011") , $impl->removePerms($actionsBitwiseValue, $actionBitwiseValueArr) );

		
	}

	public function testGetActionNames()
	{
		Config::shouldReceive('get')->once()->andReturn($this->perms);
		
		$impl = new ConfigFileResourceActionService();
		$resourceName = 'user' ;
		$actionsBitwiseValue    = bindec("1001");
		
		$arr = $impl->getActionNames($resourceName, $actionsBitwiseValue) ;
		$expectedResultsArr = array('user.add', 'user.view' ); 
		// check that these 2 arrays are the same
		$this->assertEmpty(array_merge(array_diff($arr, $expectedResultsArr), array_diff($expectedResultsArr, $arr)));
	
	}

	public function testGetActionsBitwiseValue()
	{
		Config::shouldReceive('get')->once()->andReturn($this->perms);
		
		$impl = new ConfigFileResourceActionService();
		$resourceName = 'user' ;
		
		$actionsBitwiseValue = $impl->getActionsBitwiseValue($resourceName, array('user.edit', 'user.view' , 'user.add') ) ;
		
		$this->assertSame("1011" , decbin($actionsBitwiseValue)) ;

	}


}