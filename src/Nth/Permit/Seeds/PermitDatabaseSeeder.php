<?php

namespace Nth\Permit\Seeds;

use Nth\Permit\Models\Role;
use Nth\Permit\Constants\RoleConstants;
use Illuminate\Database\Eloquent\Model as Eloquent;

class PermitDatabaseSeeder extends \Illuminate\Database\Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// set up all the required roles
		Role::create(array('name' => RoleConstants::SUPER_ADMIN  ));
		Role::create(array('name' => RoleConstants::GUEST  ));
		Role::create(array('name' => RoleConstants::USER  ));
		Role::create(array('name' => RoleConstants::OWNER  ));


	}
}
