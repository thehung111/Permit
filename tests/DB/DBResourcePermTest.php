<?php

require_once 'BaseTestCase.php';
use Nth\Permit\Helper\ConfigHelper;
use Illuminate\Support\Facades\App;
use Nth\Permit\Constants\ResourceConstants;
use Nth\Permit\Constants\RoleConstants;


class DBResourcePermTest extends BaseTestCase
{
	public function testGetResourcePerm()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = 0;
		$resourceInstancePK = "abc"; // purposely give random value which should be ignored
		$roleId = 2; // guest role
		$resourceName = 'user';

		$entry = $service->getResourcePerm($roleId, $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(0, $entry->actionsBitwiseValue);
	}

	/**
	 * @expectedException \InvalidArgumentException 
	 */ 
	public function testGetResourcePerm_InvalidInput()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;
		$resourceInstancePK = null; // resource instance must be given
		$roleId = 2; // guest role
		$resourceName = 'user';

		$service->getResourcePerm($roleId, $resourceName, $scope , $resourceInstancePK );
		
	}

	public function testGetResourcePerm_Instance()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;;
		$resourceInstancePK = "3"; 
		$roleId = 4; 
		$resourceName = 'user';

		$entry = $service->getResourcePerm($roleId, $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(10, $entry->actionsBitwiseValue);
	}

	public function testGetResourcePerm_InvalidInstance()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;;
		$resourceInstancePK = "3222"; // invalid
		$roleId = 4; 
		$resourceName = 'user';

		$entry = $service->getResourcePerm($roleId, $resourceName, $scope , $resourceInstancePK );

		$this->assertNull($entry);
	}


	/**
	 * @expectedException \InvalidArgumentException 
	 */ 
	public function testGetResourcePerms_InvalidInput()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;
		$resourceInstancePK = null; // resource instance must be given
		$roleId = 3; // guest role
		$resourceName = 'user';

		$service->getResourcePerms($resourceName, $scope , $resourceInstancePK );
		
	}

	public function testGetResourcePerms_InvalidInstance()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;;
		$resourceInstancePK = "3222"; // invalid
		$resourceName = 'user';

		$count = count($service->getResourcePerms( $resourceName, $scope , $resourceInstancePK ) );

		$this->assertSame(0, $count);
	}

	public function testGetResourcePerms()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_ALL;
		$resourceInstancePK = null; 
		$resourceName = 'user';

		$count = count($service->getResourcePerms( $resourceName, $scope , $resourceInstancePK ) );

		$this->assertSame(3, $count);
	}

	public function testGetResourcePerms_Instance()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;
		$resourceInstancePK = "5"; 
		$resourceName = 'user';

		$count = count($service->getResourcePerms( $resourceName, $scope , $resourceInstancePK ) );

		$this->assertEquals(2, $count);
	}

	public function testGetOwnerResourcePerm()
	{

		$service = App::make('permit.ResourcePermService'); 

		$resourceInstancePK = "5"; 
		$resourceName = 'user';

		$entry = $service->getOwnerResourcePerm( $resourceName , $resourceInstancePK ) ;

		$this->assertEquals(5, $entry->ownerPK);

	}

	public function testGetOwnerResourcePerm_InvalidInstance()
	{

		$service = App::make('permit.ResourcePermService'); 

		$resourceInstancePK = "5555"; 
		$resourceName = 'user';

		$entry = $service->getOwnerResourcePerm( $resourceName , $resourceInstancePK ) ;

		$this->assertNull($entry );

	}


	public function testAddResourcePerm_NewPerm()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = 0;
		$resourceInstancePK = "abc"; // purposely give random value which should be ignored
		$roleId = 2; // guest role
		$resourceName = 'user';
		$actionName = "user.edit" ;

		$entry = $service->addResourcePerm($roleId, $actionName , $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(2, $entry->actionsBitwiseValue);
	}

	public function testAddResourcePerm_ExistingPerm()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = 0;
		$resourceInstancePK = "abc"; // purposely give random value which should be ignored
		$roleId = 3; // logged in user role
		$resourceName = 'user';
		$actionName = "user.edit" ;

		$entry = $service->addResourcePerm($roleId, $actionName , $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(10, $entry->actionsBitwiseValue);
	}

	public function testAddResourcePerm_InstancePerm()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;;
		$resourceInstancePK = "4"; // purposely give random value which should be ignored
		$roleId = 3; // logged in user role
		$resourceName = 'user';
		$actionName = "user.delete" ;

		$entry = $service->addResourcePerm($roleId, $actionName , $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(4, $entry->actionsBitwiseValue);
	}

	public function testAddResourcePermRN_InstancePerm()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;
		$resourceInstancePK = "4"; // purposely give random value which should be ignored
		$roleName = RoleConstants::USER ; // logged in user role
		$resourceName = 'user';
		$actionName = "user.delete" ;

		$entry = $service->addResourcePermForRoleName($roleName, $actionName , $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(4, $entry->actionsBitwiseValue);
	}

	public function testAddResourcePermsRN()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = 0;
		$resourceInstancePK = "4"; 
		$roleName = RoleConstants::USER ; // logged in user role
		$resourceName = 'user';
		
		$entry = $service->addResourcePermsForRoleName($roleName, 4 , $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(12, $entry->actionsBitwiseValue);
	}

	public function testAddResourcePermsActionNames()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = 0;
		$resourceInstancePK = "4"; 
		$roleName = RoleConstants::USER ; // logged in user role
		$resourceName = 'user';
		$actionNames = array('user.delete' , 'user.view' );
		
		$entry = $service->addResourcePermsByActionNames(3, $actionNames , $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(12, $entry->actionsBitwiseValue);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testAddResourcePermsActionNames_OwnerRoleMissingOwnerPK()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = 0;
		$resourceInstancePK = "4"; 
		$roleName = RoleConstants::OWNER ; // logged in user role
		$resourceName = 'user';
		
		$service->addResourcePermsForRoleName($roleName, 4 , $resourceName, $scope , $resourceInstancePK );
		
	}


	public function testSetResourcePerms()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = 0;
		$resourceInstancePK = "4"; 
		$roleName = RoleConstants::USER ; // logged in user role
		$resourceName = 'user';
		$actionNames = array('user.delete' );
		
		$entry = $service->setResourcePermsByActionNames(3, $actionNames , $resourceName, $scope , $resourceInstancePK );

		$this->assertEquals(4, $entry->actionsBitwiseValue);
	}

	public function testSetOwnerResourcePerms()
	{

		$service = App::make('permit.ResourcePermService'); 

		$scope = ResourceConstants::SCOPE_INSTANCE;
		$resourceInstancePK = "4"; 
		$resourceName = 'user';
		$actionNames = array('user.delete' , 'user.view' );
		$ownerPK = 4;
		
		$entry = $service->setOwnerResourcePermsByActionNames($ownerPK,  $actionNames, $resourceName, $resourceInstancePK ) ;

		$this->assertEquals(12, $entry->actionsBitwiseValue);
	}

	public function testRemovePerms()
	{
		$init_count = $this->countAll();
		$service = App::make('permit.ResourcePermService'); 
		$service->removeResourcePerms($resourceName = 'user', $scope = 0, $resourceInstancePK = null) ;
		$after_count = $this->countAll();

		$this->assertEquals(3, $init_count - $after_count);
	}

	public function testRemovePerms_Instance()
	{
		$init_count = $this->countAll();
		$service = App::make('permit.ResourcePermService'); 
		$service->removeResourcePerms($resourceName = 'user', $scope = 1, $resourceInstancePK = "5") ;
		$after_count = $this->countAll();

		$this->assertEquals(2, $init_count - $after_count);
	}

	public function testRemovePerms_InvalidInstance()
	{
		$init_count = $this->countAll();
		$service = App::make('permit.ResourcePermService'); 
		$service->removeResourcePerms($resourceName = 'user', $scope = 1, $resourceInstancePK = "53767") ;
		$after_count = $this->countAll();

		$this->assertEquals(0, $init_count - $after_count);
	}

	public function testHasAction_View()
	{

		$scope = 0;
		$resourceInstancePK = null; 
		$roleId = 3; // user role
		$resourceName = 'user';

		$service = App::make('permit.ResourcePermService'); 
		
		$entry = $service->getResourcePerm($roleId, $resourceName, $scope , $resourceInstancePK );
		$actionService = new \Nth\Permit\Services\DatabaseResourceActionService;
		// $actionService = new \Nth\Permit\Services\ConfigFileResourceActionService;
				

		$action = $actionService->getAction($resourceName, 'user.view') ;

		$this->assertTrue($service->hasAction($entry, $action) );
		
	}

	public function testHasAction_No()
	{

		$scope = 0;
		$resourceInstancePK = null; 
		$roleId = 3; // user role
		$resourceName = 'user';

		$service = App::make('permit.ResourcePermService'); 
		
		$entry = $service->getResourcePerm($roleId, $resourceName, $scope , $resourceInstancePK );
		$actionService = new \Nth\Permit\Services\DatabaseResourceActionService;
		// $actionService = new \Nth\Permit\Services\ConfigFileResourceActionService;
		
		$action = $actionService->getAction($resourceName, 'user.delete') ;

		$this->assertFalse($service->hasAction($entry, $action) );
		
	}

	public function testHasPerms()
	{
		$service = App::make('permit.ResourcePermService'); 
		$roleIdArr = array(1,2,3);
		$actionName = 'user.delete';
		$resourceName = 'user';

		$this->assertTrue($service->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK = null, $checkOwnerPerm = false, $ownerPK = null ) );

		$roleIdArr = array(3);
		$actionName = 'user.view';
		$this->assertTrue($service->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK = null, $checkOwnerPerm = false, $ownerPK = null ) );

	}



	public function testHasPerms_No()
	{
		$service = App::make('permit.ResourcePermService'); 
		$roleIdArr = array(2,3);
		$actionName = 'user.delete';
		$resourceName = 'user';

		$this->assertFalse($service->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK = null, $checkOwnerPerm = false, $ownerPK = null ) );
		

	}

	public function testHasOwnerPerms()
	{
		$service = App::make('permit.ResourcePermService'); 
		$roleIdArr = array(3); // normal user role
		$actionName = 'user.edit';
		$resourceName = 'user';

		$this->assertTrue($service->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK = "4", $checkOwnerPerm = true, $ownerPK = 4 ) );

	}

	public function testHasOwnerPerms_No()
	{
		$service = App::make('permit.ResourcePermService'); 
		$roleIdArr = array(3); // normal user role
		$actionName = 'user.delete'; // an user cannot delete himself
		$resourceName = 'user';

		$this->assertFalse($service->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK = "4", $checkOwnerPerm = true, $ownerPK = 4 ) );

	}

	public function testHasPerms_UserView()
	{
		$service = App::make('permit.ResourcePermService'); 
		$roleIdArr = array(3);
		$actionName = 'user.view';
		$resourceName = 'user';

		$this->assertTrue($service->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK = null, $checkOwnerPerm = false, $ownerPK = null ) );


	}

	public function testHasPerms_UserEdit()
	{
		$service = App::make('permit.ResourcePermService'); 
		$roleIdArr = array(2,3);
		$actionName = 'user.edit';
		$resourceName = 'user';

		$this->assertFalse($service->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK = null, $checkOwnerPerm = false, $ownerPK = null ) );


	}


	public function countAll(){
		return DB::table(ConfigHelper::getResourcePermTableName())->count();
	
	}

}