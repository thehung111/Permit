<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Nth\Permit\Helper\ConfigHelper;

/**
 * Migration. Create all the necessary tables. Must run database seeding afterwards to create all built in roles.
 */ 
class CreateUserRoleResourcePermissionTables extends Migration {

	// reference for various commands: http://laravel.com/docs/schema


	function addUserColumnToTable($table, $user_pk_name, $user_pk_type) 
	{
		$col = null ;
		switch ($user_pk_type) {
			case 'string':
				$col = $table->string($user_pk_name, 64);
				break;

			case 'integer':
				$col = $table->integer($user_pk_name);
				break;

			case 'bigInteger':
				$col = $table->bigInteger($user_pk_name);
				break;

			case 'integer_unsigned':
				$col = $table->integer($user_pk_name)->unsigned();
				break;

			case 'bigInteger_unsigned':
				$col = $table->bigInteger($user_pk_name)->unsigned();
				break;
			
			default: // default use integer unsigned
				$col = $table->integer($user_pk_name)->unsigned();
				break;
		}

		if($user_pk_name == 'ownerPK'){
			$col->nullable();
		}
		
	}


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$user_pk_name = ConfigHelper::getUserPKName();
		$user_pk_type = ConfigHelper::getUserPKDataType();	
		$that = $this;

		Schema::create(ConfigHelper::getRoleTableName(), function($table)
		{
			$table->increments('roleId')->unsigned();
			$table->string('name', 64)->unique();
			$table->dateTime('createDate');
			$table->dateTime('modifiedDate');
		});


		Schema::create(ConfigHelper::getUserRoleTableName(), function($table) use ($that , $user_pk_name, $user_pk_type)
		{
			$table->integer('roleId')->unsigned();

			$that->addUserColumnToTable($table, $user_pk_name, $user_pk_type); 
			
			$table->foreign('roleId' ,'FK_user_roleId')->references('roleId')->on( ConfigHelper::getRoleTableName() );
			
		});



		Schema::create(ConfigHelper::getResourceActionTableName(), function($table)
		{
			$table->increments('resourceActionId')->unsigned();
			$table->string('resourceName', 255);	// name of the resource
			$table->string('actionName', 64);		// one of the possible action or permission can be applied to this resource
			$table->integer('bitwiseValue');		// the bit value for this action in power of 2

			$table->unique(array('resourceName', 'actionName') , 'IX_resource_action_unique');
			$table->index('resourceName', 'IX_resourceName');
		});


		Schema::create(ConfigHelper::getResourcePermTableName(), function($table) use ($that , $user_pk_type)
		{
			$table->increments('resourcePermId')->unsigned();
			$table->string('resourceName', 255);
			$table->integer('roleId')->unsigned();
			$table->integer('actionsBitwiseValue'); 	// store the permission which is sum of granted actions
			$table->tinyInteger('scope')->unsigned();	// is this an individual scope or generic scope that apply to all instances
			$table->string('resourceInstancePK', 255)->nullable();	// if this is individual scope, a value will be set

			// ownerPK column is needed
			$that->addUserColumnToTable($table, 'ownerPK', $user_pk_type); 	// if roleId is owner and scope is individual, there will be a value for owner
			

			$table->foreign('roleId' ,'FK_resource_perm_roleId')->references('roleId')->on( ConfigHelper::getRoleTableName() );
			$table->unique(array('resourceName', 'roleId' , 'scope', 'resourceInstancePK') , 'IX_unique_R_R_S_R');
		});

		// let's call db seeding here


	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//drop foreign key first
		Schema::table(ConfigHelper::getResourcePermTableName(), function($table)
		{
		    $table->dropForeign('FK_resource_perm_roleId');
		});

		Schema::table(ConfigHelper::getUserRoleTableName(), function($table)
		{
		    $table->dropForeign('FK_user_roleId');
		});

		Schema::drop(ConfigHelper::getResourcePermTableName());
		Schema::drop(ConfigHelper::getUserRoleTableName());
		Schema::drop(ConfigHelper::getRoleTableName());
		Schema::drop(ConfigHelper::getResourceActionTableName());
	}

}
