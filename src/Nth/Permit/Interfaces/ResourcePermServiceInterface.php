<?php

namespace Nth\Permit\Interfaces;

use Nth\Permit\Models\ResourcePerm;
use Nth\Permit\Models\ResourceAction;

interface ResourcePermServiceInterface
{
	
	/**
	 * Get back permissions that this role has for this resource
	 * 
	 * @param int $roleId 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function getResourcePerm($roleId, $resourceName, $scope = 0, $resourceInstancePK = null);

	/**
	 * Get back permissions for this resource
	 * 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return list of ResourcePerm
	 */
	public function getResourcePerms($resourceName, $scope = 0, $resourceInstancePK = null);



	/**
	 * Get owner permission for this resource
	 * 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function getOwnerResourcePerm($resourceName, $resourceInstancePK );


	/**
	 * Grants the role permission at the scope to perform the action on resources of the type $resourceName.
	 * If scope is all, then resourceInstancePK will be ignored (stored as null in database)
	 * If scope is instance, then resourceInstancePK is required.
	 * 
	 * @param int $roleId 
	 * @param string $actionName 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function addResourcePerm($roleId, $actionName, $resourceName, $scope = 0, $resourceInstancePK = null) ;

	/**
	 * Grants the role permission at the scope to perform the action on resources of the type $resourceName.
	 * If scope is all, then resourceInstancePK will be ignored (stored as null in database)
	 * If scope is instance, then resourceInstancePK is required.
	 * 
	 * @param string $roleName 
	 * @param string $actionName
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function addResourcePermForRoleName($roleName, $actionName, $resourceName, $scope = 0, $resourceInstancePK = null) ;


	/**
	 * Grants the role permission at the scope to perform the actions on resources of the type $resourceName.
	 * If scope is all, then resourceInstancePK will be ignored (stored as null in database)
	 * If scope is instance, then resourceInstancePK is required.
	 * 
	 * @param int $roleId 
	 * @param int $actionsBitwiseValue 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK
	 * @return ResourcePerm
	 */
	public function addResourcePerms($roleId, $actionsBitwiseValue, $resourceName, $scope = 0, $resourceInstancePK = null) ;

	/**
	 * Grants the role permission at the scope (by actionsBitwiseValue) to perform the actions on resources of the type $resourceName.
	 * If scope is all, then resourceInstancePK will be ignored (stored as null in database)
	 * If scope is instance, then resourceInstancePK is required.
	 * 
	 * @param int $roleName 
	 * @param int $actionsBitwiseValue 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function addResourcePermsForRoleName($roleName, $actionsBitwiseValue, $resourceName, $scope = 0, $resourceInstancePK = null) ;


	/**
	 * Grants the role permission (by actionNames) at the scope to perform the actions on resources of the type $resourceName.
	 * If scope is all, then resourceInstancePK will be ignored (stored as null in database)
	 * If scope is instance, then resourceInstancePK is required.
	 * 
	 * @param int $roleId 
	 * @param array $actionNames (array of string)
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function addResourcePermsByActionNames($roleId, array $actionNames, $resourceName, $scope = 0, $resourceInstancePK = null) ;

	

	/**
	 * Update the role permission (by actionNames) at the scope to perform the actions on resources of the type $resourceName.
	 * If scope is all, then resourceInstancePK will be ignored (stored as null in database)
	 * If scope is instance, then resourceInstancePK is required.
	 * 
	 * @param int $roleId 
	 * @param array $actionNames (array of string)
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function setResourcePermsByActionNames($roleId, array $actionNames, $resourceName, $scope = 0, $resourceInstancePK = null) ;

	/**
	 * Update the role permission (by actionsBitwiseValue) at the scope to perform the actions on resources of the type $resourceName.
	 * If scope is all, then resourceInstancePK will be ignored (stored as null in database)
	 * If scope is instance, then resourceInstancePK is required.
	 * 
	 * @param int $roleId 
	 * @param int $actionsBitwiseValue 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function setResourcePerms($roleId, $actionsBitwiseValue, $resourceName, $scope = 0, $resourceInstancePK = null) ;



	/**
	 * Grant owner role permission (by actionsBitwiseValue) to perform the actions on resources of the type $resourceName.
	 * The role will be built in owner role, the scope is instance scope
	 * 
	 * @param mixed $ownerPK (depend on config file, the data type for ownerPK can be int or string)
	 * @param int $actionsBitwiseValue 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */	
	public function setOwnerResourcePerms($ownerPK, $actionsBitwiseValue, $resourceName, $resourceInstancePK ) ;

	/**
	 * Grant owner role permission (by actionNames) to perform the actions on resources of the type $resourceName.
	 * The role will be built in owner role, the scope is instance scope
	 * 
	 * @param mixed $ownerPK (depend on config file, the data type for ownerPK can be int or string)
	 * @param array $actionNames 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */	
	public function setOwnerResourcePermsByActionNames($ownerPK, array $actionNames, $resourceName, $resourceInstancePK ) ;


	

	/**
	 * Remove all resource permissions at the scope to resources of the type
	 * 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return void
	 */
	public function removeResourcePerms($resourceName, $scope = 0, $resourceInstancePK = null) ;



	/**
	 * Returns true if the resource permission grants permission to perform the resource action
	 * 
	 * @param ResourcePerm $resourcePermission 
	 * @param ResourceAction $resourceAction 
	 * @return bool
	 */
	public function hasAction(ResourcePerm $resourcePermission, ResourceAction $resourceAction) ;


	/**
	 * Return true if the given roles have access to a resource by the action
	 * Internally, this method will check all the available scopes (first all scope then instance scope, etc)
	 * if $checkOwnerPerm is true, ownerPK must be provided. This is to verify that the user has the owner permission
	 * 
	 * @param array $roleIdArr 
	 * @param string $actionName 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK optional
	 * @param bool $checkOwnerPerm
	 * @param mixed $ownerPK
	 * @return bool
	 */
	public function hasPerm(array $roleIdArr, $actionName, $resourceName, $resourceInstancePK = null, $checkOwnerPerm = false, $ownerPK = null );


	/**
	 * Return true if given roles have access to a resource in a given scope
	 * 
	 * @param array $roleIdArr 
	 * @param string $actionName 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return bool
	 */
	public function hasScopePerm(array $roleIdArr, $actionName, $resourceName, $scope , $resourceInstancePK  );


	/**
	 * Return true of given roles have access to a resource based on a list of scopes.
	 * if $checkOwnerPerm is true, ownerPK must be provided. This is to verify that the user has the owner permission.
	 * This method cater for future enhancements when it needs to have more fine-grained permission hierarchy (more scopes, more than the built in two)
	 * e.g. If need permission for a property of an instance. For example, edit only title for a blog post
	 * If that is the case, more scopes will be needed
	 * 
	 * @param array $roleIdArr 
	 * @param string $actionName 
	 * @param string $resourceName 
	 * @param array $scopeArr 
	 * @param array $resourceInstancePKArr 
	 * @param bool $checkOwnerPerm
	 * @param mixed $ownerPK
	 * @return bool
	 */
	public function hasScopePerms(array $roleIdArr, $actionName, $resourceName, array $scopeArr , array $resourceInstancePKArr, $checkOwnerPerm = false, $ownerPK = null);



}