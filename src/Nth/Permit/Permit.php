<?php

namespace Nth\Permit;
use Illuminate\Support\Facades\Log;
use Nth\Permit\Helper\ConfigHelper;
use Illuminate\Support\Facades\Auth;

/**
*  Permission Service
*/
class Permit implements \Nth\Permit\Interfaces\PermissionCheckerInterface
{
	
	protected $roleService;
	protected $resourceActionService;
	protected $resourcePermService;

	function __construct(\Nth\Permit\Interfaces\RoleServiceInterface $roleService, 
						\Nth\Permit\Interfaces\ResourceActionServiceInterface $resourceActionService,
						\Nth\Permit\Interfaces\ResourcePermServiceInterface $resourcePermService
						)
	{
		$this->roleService = $roleService;
		$this->resourceActionService = $resourceActionService;
		$this->resourcePermService = $resourcePermService;
	}

	public function getRoleService()
	{
		return $roleService;
	}

	public function getResourceActionService()
	{
		return $resourceActionService;
	}

	public function getResourcePermService()
	{
		return $resourcePermService;
	}

	
	/**
	 * Return the role id for "Owner" role
	 * 
	 * @return int
	 */
	public function getOwnerRoleId() 
	{
		return $this->roleService->getOwnerRoleId() ;
	}

	/**
	 * Return the role id for "Guest" role
	 * 
	 * @return int
	 */
	public function getGuestRoleId() 
	{
		return $this->roleService->getGuestRoleId() ;
	}

	/**
	 * Return the role id for "User" role, a logged in user
	 * 
	 * @return int
	 */
	public function getRegularUserRoleId() 
	{
		return $this->roleService->getRegularUserRoleId() ;
	}

	/**
	 * Return the role id for "Super Admin" role
	 * 
	 * @return int
	 */
	public function getSuperAdminRoleId() 
	{
		return $this->roleService->getSuperAdminRoleId() ;
	}



	/**
	 * Return the role ids for a given userId
	 * 
	 * @param mixed $userId 
	 * @return array
	 */
	public function getRoleIds($userId) 
	{
		return $this->roleService->getRoleIds($userId) ;
	}

	/**
	 * Return the roles for a given userId
	 * 
	 * @param mixed $userId 
	 * @return array
	 */
	public function getRoles($userId) 
	{
		return $this->roleService->getRoles($userId);
	}

	/**
	 * Return current logged in user id or null
	 * 
	 * @return Illuminate\Auth\UserInterface|null
	 */
	public function getUser()
	{
		if($this->isSignedIn())
			return Auth::user();
		return null;
	}

	/**
	 * Return current logged in user id or null
	 * 
	 * @return mixed
	 */
	public function getUserId()
	{
		if($this->isSignedIn())
		{
			$user = $this->getUser();
			return $user->getAuthIdentifier();
		}

		return null;
	}


	/**
	 * return list of role ids for current logged in user or array(Guest) role if not logged in
	 * 
	 * @return array
	 */
	public function getUserRoleIds()
	{
		if($this->isSignedIn())
		{
			$userId = $this->getUserId();
			return $this->getRoleIds($userId);
		}
		else
			return array( $this->getGuestRoleId() );
	}

	/**
	 * return list of roles for current logged in user or array(Guest) role if not logged in
	 * 
	 * @return array
	 */
	public function getUserRoles() 
	{

		if($this->isSignedIn())
		{
			$userId = $this->getUserId();
			return $this->getRoles($userId);
		}
		else
		{
			return array( $this->getRoleService()->getGuestRole() );
		}
	}


	/**
	 * Check if user is logged in
	 * 
	 * @return bool
	 */
	public function isSignedIn()
	{
		return Auth::check();
	}


	/**
	 * Check if user is super admin
	 * 
	 * @return bool
	 */
	public function isSuperAdmin()
	{
		if($this->isSignedIn())
		{
			$roles = getUserRoles() ;
			$superAdminRoleId = $this->getSuperAdminRoleId();
			foreach ($roles  as $role) {
				if($role->roleId == $superAdminRoleId  )
					return true;
			}
		}

		return false;
	}



	/**
	 * Check if user has this role
	 * 
	 * @param string $roleName 
	 * @return bool
	 */
	public function hasRole($roleName)
	{
		if(empty($roleName) )
			throw new \InvalidArgumentException('Role name must not be empty.');

		$roles = getUserRoles() ;
			
		foreach ($roles  as $role) {
			if($role->name == $roleName  )
				return true;
		}

		return false;
	}


	/**
	 * Returns true if the user is the owner of the resource and has permission to perform the action
	 * 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK 
	 * @param string $actionName 
	 * @return bool
	 */
	public function hasOwnerPermission( $actionName, $resourceName, $resourceInstancePK  )
	{
		$roleIdArr = $this->getUserRoles() ;
		$ownerId = $this->getUserId();
		return $this->getResourcePermService()->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK , true, $ownerId );
	}

	/**
	 * Returns true if the user is the owner of the resource and has permission to perform the action
	 * 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK 
	 * @param string $actionName 
	 * @return bool
	 */
	public function hasPermission($actionName, $resourceName, $resourceInstancePK = null)
	{
		$roleIdArr = $this->getUserRoles() ;
		$checkOwnerPerm = (!is_null($resourceInstancePK) );
		$ownerId = $this->getUserId();
		return $this->getResourcePermService()->hasPerm($roleIdArr, $actionName, $resourceName, $resourceInstancePK , $checkOwnerPerm, $ownerId );
	
	}


	
}

