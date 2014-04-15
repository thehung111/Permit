<?php

require_once 'BaseTestCase.php';
use Nth\Permit\Helper\ConfigHelper;

class DBResourceActionServiceTest extends BaseTestCase
{

	private function getTableCount(){
		return DB::table(ConfigHelper::getResourceActionTableName())->count();
	}

	public function testAddActions()
	{
		$init_count = $this->getTableCount();

		$resourceName = 'blog' ;
		
		$actionArray = array(
				array('actionName' => 'post.add'		, 'bitwiseValue' => 1),
				array('actionName' => 'post.edit'		, 'bitwiseValue' => 2),
				array('actionName' => 'post.delete'		, 'bitwiseValue' => 4),
				array('actionName' => 'post.view'		, 'bitwiseValue' => 8),
			);

		$service = new \Nth\Permit\Services\DatabaseResourceActionService; 
		$service->addActions($resourceName, $actionArray ); 

		// should have 4 entries being added
		$addedActionsCount = $this->getTableCount() - $init_count;

		$this->assertEquals( count($actionArray) , $addedActionsCount );
	}

	public function testAddActions_Empty()
	{
		$init_count = $this->getTableCount();


		$resourceName = 'blog' ;
		
		$actionArray = array(
			);

		$service = new \Nth\Permit\Services\DatabaseResourceActionService; 
		$service->addActions($resourceName, $actionArray ); 

		$addedActionsCount = $this->getTableCount() - $init_count;

		$this->assertEquals( 0 , $addedActionsCount ); // none should be added
	}

	public function testAddActions_Exist()
	{
		$init_count = $this->getTableCount();

		$resourceName = 'user' ;
		
		// only user.sleep should be added
		$actionArray = array(
				array('actionName' => 'user.add'		, 'bitwiseValue' => 1), // already exist
				array('actionName' => 'user.edit'		, 'bitwiseValue' => 2), // already exist
				array('actionName' => 'user.sleep'		, 'bitwiseValue' => 32),
				array('actionName' => 'user.watch_soccer'		, 'bitwiseValue' => 8), // bitwiseValue is not unique
			);

		$service = new \Nth\Permit\Services\DatabaseResourceActionService; 
		$service->addActions($resourceName, $actionArray ); 

		// should have 4 entries being added
		$addedActionsCount = $this->getTableCount() - $init_count;

		$this->assertEquals( 1 , $addedActionsCount );
	}

	public function testRemoveActions_Empty()
	{
		$init_count = $this->getTableCount();

		$resourceName = 'user' ;
		
		$actionArray = array(
			);

		$service = new \Nth\Permit\Services\DatabaseResourceActionService; 
		$service->removeActions($resourceName, $actionArray ); 

		$removedActionsCount = $init_count - $this->getTableCount() ;

		$this->assertEquals( 0 , $removedActionsCount ); // none should be added
	}

	public function testRemoveActions()
	{
		$init_count = $this->getTableCount();

		$resourceName = 'user' ;
		
		$actionArray = array('user.add', 'user.xxx' , 'user.test' , 'user.delete');

		$service = new \Nth\Permit\Services\DatabaseResourceActionService; 
		$service->removeActions($resourceName, $actionArray ); 

		$removedActionsCount = $init_count - $this->getTableCount() ;

		$this->assertEquals( 2 , $removedActionsCount ); // none should be added
	}

	public function testRemoveAllActions()
	{
		$init_count = $this->getTableCount();

		$resourceName = 'user' ;
		
		$service = new \Nth\Permit\Services\DatabaseResourceActionService; 
		$service->removeAllActions($resourceName ); 

		$removedActionsCount = $init_count - $this->getTableCount() ;

		$this->assertEquals( 4 , $removedActionsCount ); // none should be added
	}

	public function testSetActions()
	{
		$resourceName = 'user' ;
		
		// only user.sleep should be added
		$actionArray = array(
				array('actionName' => 'user.add'		, 'bitwiseValue' => 64), // already exist
				array('actionName' => 'user.edit'		, 'bitwiseValue' => 128), // already exist
				array('actionName' => 'user.sleep'		, 'bitwiseValue' => 32),
				array('actionName' => 'user.watch_soccer' , 'bitwiseValue' => 8), 
			);

		$service = new \Nth\Permit\Services\DatabaseResourceActionService; 
		$service->setActions($resourceName, $actionArray ); 

		$db_actions = $service->getSupportedActions($resourceName);

		$this->assertEquals( count($actionArray) , count($db_actions) );

		$r1 = $this->extractKeyArr($actionArray, 'actionName');
		$r2 = $this->extractKeyArr($db_actions, 'actionName');

		$this->assertArraySame($r1, $r2);

		$r3 = $this->extractKeyArr($actionArray, 'bitwiseValue');
		$r4 = $this->extractKeyArr($db_actions, 'bitwiseValue');

		$this->assertArraySame($r3, $r4);

	}

}