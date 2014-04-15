<?php

use Nth\Permit\Helper\ConfigHelper;
use Mockery as m;
use Illuminate\Support\Facades\Config;
use Nth\Permit\Services\DatabaseResourceActionService;

class DatabaseResourceActionServiceTest extends PHPUnit_Framework_TestCase
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
		$impl = new DatabaseResourceActionService();

		$resourceName = null ;
		$actionName = '' ;
		$impl->checkActionExists($resourceName, $actionName);
	}

	/**
	* @expectedException \Nth\Permit\Exceptions\NoSuchResourceActionException
	*/
    public function testcheckActionExists_InvalidAction()
	{
		
		$serviceMock = Mockery::mock('Nth\Permit\Services\DatabaseResourceActionService')->makePartial();
		$serviceMock ->shouldReceive('getActionByNames')->once()->andReturn(null);
		
		$resourceName = 'user' ;
		$actionName = 'user.sleep' ;
		$serviceMock->checkActionExists($resourceName, $actionName);
	}

    public function testcheckActionExists_ValidAction()
	{
		$mockObj = (object) array('resourceActionId' => 2, 'resourceName' => 'user', 'actionName' => 'user.add', 'bitwiseValue' => 1 );


		$serviceMock = Mockery::mock('Nth\Permit\Services\DatabaseResourceActionService')->makePartial();
		$serviceMock ->shouldReceive('getActionByNames')->once()->andReturn($mockObj);
		
		$resourceName = 'user' ;
		$actionName = 'user.add' ;
		$serviceMock->checkActionExists($resourceName, $actionName);
	}



}