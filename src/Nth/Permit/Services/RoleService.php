<?php

namespace Nth\Permit\Services;

use Nth\Permit\Models\Role;
use Nth\Permit\Constants\RoleConstants;
use Illuminate\Support\Facades\DB;
use Nth\Permit\Helper\ConfigHelper;


class RoleService implements \Nth\Permit\Interfaces\RoleServiceInterface
{
	/**
	 * add role by name. Return the newly created id
	 * 
	 * @param string $roleName 
	 * @return int 
	 */
	public function addRole($roleName)
	{
		if(empty($roleName) )
			throw new \InvalidArgumentException('Role name must not be empty.');


		$role = new Role;
		$role->name = $roleName;
		$role->save();
	}

	/**
	 * remove role by name
	 * 
	 * @param string $roleName 
	 * @return bool
	 */
	public function removeRole($roleName)
	{
		if(empty($roleName) )
			throw new \InvalidArgumentException('Role name must not be empty.');

		return Role::where('name', '=', $roleName)->delete();
	}


	/**
	 * update roleName
	 * 
	 * @param int $roleId 
	 * @param string $roleName 
	 * @return void
	 */
	public function editRole($roleId, $roleName)
	{
		if(empty($roleName) )
			throw new \InvalidArgumentException('Role name must not be empty.');

		if(!is_int($roleId))
			throw new \InvalidArgumentException("Invalid Role id: $roleId. Input is not int");

		$role = Role::find($roleId);
		if(!is_null($role))
		{
			$role->name =  $roleName;
			$role->save();
		}
	}


	/**
	 * get role id by name
	 * 
	 * @param string $roleName 
	 * @return int|null
	 */
	public function getRoleId($roleName)
	{
		// TODO: implement caching for default roles
		$role = $this->getRoleByName($roleName);

		if(is_null($role))
			return null;

		return  $role->roleId ;
	}

	/**
	 * get role by name
	 * 
	 * @param string $roleName 
	 * @return Role
	 */
	public function getRoleByName($roleName)
	{
		if(empty($roleName) )
			throw new \InvalidArgumentException('Role name must not be empty.');

		$role = Role::where('name', '=', $roleName)->first();

		return $role;
	}


	/**
	 * get guest role 
	 * 
	 * @param string $roleName 
	 * @return Role
	 */
	public function getGuestRole() 
	{
		return $this->getRoleByName(RoleConstants::GUEST);
	}


	/**
	 * Return the role id for "Owner" role
	 * 
	 * @return int
	 */
	public function getOwnerRoleId() 
	{
		return $this->getRoleId(RoleConstants::OWNER);
	}

	/**
	 * Return the role id for "Guest" role
	 * 
	 * @return int
	 */
	public function getGuestRoleId() 
	{
		return $this->getRoleId(RoleConstants::GUEST);
	}




	/**
	 * Return the role id for "User" role, a logged in user
	 * 
	 * @return int
	 */
	public function getRegularUserRoleId() 
	{
		return $this->getRoleId(RoleConstants::USER);
	}

	/**
	 * Return the role id for "Super Admin" role
	 * 
	 * @return int
	 */
	public function getSuperAdminRoleId() 
	{
		return $this->getRoleId(RoleConstants::SUPER_ADMIN);
	}

	/**
	 * Get all roles
	 * 
	 * @return array of \Nth\Permit\Models\Role
	 */
	public function getAllRoles()
	{
		return Role::all();
	}

	/**
	 * Return the role ids for a given userId
	 * 
	 * @param mixed $userId 
	 * @return array
	 */
	public function getRoles($userId) 
	{
		// $results = DB::table(ConfigHelper::getUserRoleTableName())->where('userId', '=', $userId)->get();
		$role_table_name = ConfigHelper::getRoleTableName();
		$user_role_table_name = ConfigHelper::getUserRoleTableName();

		return DB::table($role_table_name )
            		->join($user_role_table_name, $user_role_table_name . '.roleId', '=', $role_table_name .'.roleId')
            		->get();
	}


	/**
	 * Return the role ids for a given userId
	 * 
	 * @param mixed $userId 
	 * @return array
	 */
	public function getRoleIds($userId) 
	{
		$arr = array();

		$results = DB::table(ConfigHelper::getUserRoleTableName())->where('userId', '=', $userId)->get();
		foreach($results as $r)
		{
			$arr[] = $r->roleId;
		}

		return $arr;
	}

}