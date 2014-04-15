<?php

namespace Nth\Permit\Interfaces;

interface RoleServiceInterface
{
	/**
	 * add role by name. Return the newly created id
	 * 
	 * @param string $roleName 
	 * @return int 
	 */
	public function addRole($roleName);

	/**
	 * remove role by name
	 * 
	 * @param string $roleName 
	 * @return bool
	 */
	public function removeRole($roleName);


	/**
	 * update roleName
	 * 
	 * @param int $roleId 
	 * @param string $roleName 
	 * @return void
	 */
	public function editRole($roleId, $roleName);


	/**
	 * get role id by name
	 * 
	 * @param string $roleName 
	 * @return int|null
	 */
	public function getRoleId($roleName);


	/**
	 * get role by name
	 * 
	 * @param string $roleName 
	 * @return Role
	 */
	public function getRoleByName($roleName);

	/**
	 * get guest role 
	 * 
	 * @param string $roleName 
	 * @return Role
	 */
	public function getGuestRole() ;

	/**
	 * Get all roles
	 * 
	 * @return array of \Nth\Permit\Models\Role
	 */
	public function getAllRoles();


	/**
	 * Return the role id for "Owner" role
	 * 
	 * @return int
	 */
	public function getOwnerRoleId() ;

	/**
	 * Return the role id for "Guest" role
	 * 
	 * @return int
	 */
	public function getGuestRoleId() ;

	/**
	 * Return the role id for "User" role, a logged in user
	 * 
	 * @return int
	 */
	public function getRegularUserRoleId() ;

	/**
	 * Return the role id for "Super Admin" role
	 * 
	 * @return int
	 */
	public function getSuperAdminRoleId() ;


	/**
	 * Return the role ids for a given userId
	 * 
	 * @param mixed $userId 
	 * @return array
	 */
	public function getRoles($userId) ;


	/**
	 * Return the role ids for a given userId
	 * 
	 * @param mixed $userId 
	 * @return array
	 */
	public function getRoleIds($userId) ;


}
