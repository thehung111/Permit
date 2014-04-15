<?php

namespace Nth\Permit\Seeds;

use Nth\Permit\Models\ResourceAction;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Nth\Permit\Models\Role;
use Nth\Permit\Constants\RoleConstants;
use Nth\Permit\Models\ResourcePerm;



class PermitTestDatabaseSeeder extends \Illuminate\Database\Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->setupDefaultRoles();
		$this->setupResourceActions();
		$this->setupResourcePerms();

	}


	public function setupDefaultRoles(){
		
		
		Role::create(array('name' => RoleConstants::SUPER_ADMIN  ));
		Role::create(array('name' => RoleConstants::GUEST  ));
		Role::create(array('name' => RoleConstants::USER  ));
		Role::create(array('name' => RoleConstants::OWNER  ));
	}


	public function setupResourceActions(){
		
		ResourceAction::create(
			array( 'resourceName' => 'user', 'actionName' => 'user.add'		, 'bitwiseValue' => 1)
		);
		
		ResourceAction::create(
			array( 'resourceName' => 'user', 'actionName' => 'user.edit'	, 'bitwiseValue' => 2)
		);
			
		ResourceAction::create(
			array( 'resourceName' => 'user', 'actionName' => 'user.delete'	, 'bitwiseValue' => 4)
		);	

		ResourceAction::create(
			array( 'resourceName' => 'user', 'actionName' => 'user.view'	, 'bitwiseValue' => 8)
		);	
	}

	public function setupResourcePerms()
	{
		ResourcePerm::create(

			array( 'resourceName' => 'user', 
				   'roleId' => 2 ,  // guest
				   'actionsBitwiseValue' => 0, // cannot do anything
				   'scope' => 0,
				   'resourceInstancePK' => null,
				   'ownerPK' => null
			)
		);

		ResourcePerm::create(

			array( 'resourceName' => 'user', 
				   'roleId' => 3 ,  // logged in user role
				   'actionsBitwiseValue' => 8, // can view other users
				   'scope' => 0,
				   'resourceInstancePK' => null,
				   'ownerPK' => null
			)
		);

		ResourcePerm::create(

			array( 'resourceName' => 'user', 
				   'roleId' => 1 ,  // super admin role
				   'actionsBitwiseValue' => 15, // can do all actions
				   'scope' => 0,
				   'resourceInstancePK' => null,
				   'ownerPK' => null
			)
		);

		ResourcePerm::create(

			array( 'resourceName' => 'user', 
				   'roleId' => 4 ,  // owner role
				   'actionsBitwiseValue' => 10, // can view and edit his own data (8 + 2)
				   'scope' => 1,
				   'resourceInstancePK' => "3",
				   'ownerPK' => 3
			)
		);
		
		ResourcePerm::create(

			array( 'resourceName' => 'user', 
				   'roleId' => 4 ,  // owner role
				   'actionsBitwiseValue' => 10, // can view and edit his own data (8 + 2)
				   'scope' => 1,
				   'resourceInstancePK' => "4",
				   'ownerPK' => 4
			)
		);

		ResourcePerm::create(

			array( 'resourceName' => 'user', 
				   'roleId' => 4 ,  // owner role
				   'actionsBitwiseValue' => 10, // can view and edit his own data (8 + 2)
				   'scope' => 1,
				   'resourceInstancePK' => "5",
				   'ownerPK' => 5
			)
		);

		ResourcePerm::create(

			array( 'resourceName' => 'user', 
				   'roleId' => 2 ,  // guest role
				   'actionsBitwiseValue' => 8, // allow guest to view this resource
				   'scope' => 1,
				   'resourceInstancePK' => "5",
				   'ownerPK' => null
			)
		);

	}


}
