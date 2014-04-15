<?php

namespace Nth\Permit\Interfaces;

interface PermissionCheckerInterface
{
	
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
	public function getRoleIds($userId) ;

	/**
	 * Return the roles for a given userId
	 * 
	 * @param mixed $userId 
	 * @return array
	 */
	public function getRoles($userId) ;

	/**
	 * Return current logged in user id or null
	 * 
	 * @return Illuminate\Auth\UserInterface|null
	 */
	public function getUser();



	/**
	 * Return current logged in user id or null
	 * 
	 * @return mixed
	 */
	public function getUserId();


	/**
	 * return list of role ids for current logged in user or array(Guest) role if not logged in
	 * 
	 * @return array
	 */
	public function getUserRoleIds();

	/**
	 * return list of roles for current logged in user or array(Guest) role if not logged in
	 * 
	 * @return array
	 */
	public function getUserRoles() ;


	/**
	 * Check if user is logged in
	 * 
	 * @return bool
	 */
	public function isSignedIn();


	/**
	 * Check if user is super admin
	 * 
	 * @return bool
	 */
	public function isSuperAdmin();


	/**
	 * Check if user has this role
	 * 
	 * @param string $roleName 
	 * @return bool
	 */
	public function hasRole($roleName);


	/**
	 * Returns true if the user is the owner of the resource and has permission to perform the action
	 * 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK 
	 * @param string $actionName 
	 * @return bool
	 */
	public function hasOwnerPermission( $actionName, $resourceName, $resourceInstancePK  ); 

	/**
	 * Returns true if the user is the owner of the resource and has permission to perform the action
	 * 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK 
	 * @param string $actionName 
	 * @return bool
	 */
	public function hasPermission($actionName, $resourceName, $resourceInstancePK = null); 



}
