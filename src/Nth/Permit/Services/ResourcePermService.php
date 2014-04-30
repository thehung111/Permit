<?php

namespace Nth\Permit\Services;

use Nth\Permit\Models\Role;
use Nth\Permit\Models\ResourcePerm;
use Nth\Permit\Models\ResourceAction;
use Nth\Permit\Constants\ResourceConstants;
use Nth\Permit\Helper\ConfigHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Very important class that generate all the complex queries for permission checking.
 */
class ResourcePermService implements \Nth\Permit\Interfaces\ResourcePermServiceInterface
{
	function __construct(\Nth\Permit\Interfaces\RoleServiceInterface $roleService, 
						\Nth\Permit\Interfaces\ResourceActionServiceInterface $resourceActionService
						)
	{
		$this->roleService = $roleService;
		$this->resourceActionService = $resourceActionService;
	}


	/**
	 * Check if inputs are missing or not valid
	 * 
	 * @param int $roleId 
	 * @param string $actionName 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function checkAllInputsWithActionName($roleId, $actionName, $resourceName, $scope = 0, $resourceInstancePK = null)
	{
		if(empty($actionName) )
			throw new \InvalidArgumentException('Action name must not be empty.');

		$this->checkAllInputs($roleId, $resourceName, $scope , $resourceInstancePK );
	}
	
	/**
	 * Check if inputs are missing or not valid
	 * 
	 * @param int $roleId 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function checkAllInputs($roleId, $resourceName, $scope = 0, $resourceInstancePK = null)
	{
		
		if(!is_int($roleId))
			throw new \InvalidArgumentException("Role id is not int: $roleId");

		$this->checkInputs($resourceName, $scope , $resourceInstancePK );
	}


	/**
	 * Check if inputs are missing or not valid
	 * 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function checkInputs($resourceName, $scope = 0, $resourceInstancePK = null)
	{
		if(empty($resourceName) )
			throw new \InvalidArgumentException('Resource name must not be empty.');

		if(!is_int($scope))
			throw new \InvalidArgumentException("Scope is not int: $scope");

		if($scope == ResourceConstants::SCOPE_INSTANCE)
		{
			if(empty($resourceInstancePK ))
				throw new \InvalidArgumentException('resourceInstancePK cannot be empty for instance scope.');
		}
	}

	/**
	 * Get back permissions that this role has for this resource
	 * 
	 * @param int $roleId 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function getResourcePerm($roleId, $resourceName, $scope = 0, $resourceInstancePK = null)
	{
		$this->checkAllInputs($roleId, $resourceName, $scope , $resourceInstancePK ); // check if input valid

		$query = ResourcePerm::where('roleId', '=', $roleId)->where('resourceName', '=', $resourceName)->where('scope', '=', $scope);
		if($scope == ResourceConstants::SCOPE_INSTANCE)
		{
			$query = $query->where('resourceInstancePK', '=', $resourceInstancePK);
		}
		return $query->first();	
	}

	/**
	 * Get back permissions for this resource
	 * 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return list of ResourcePerm
	 */
	public function getResourcePerms($resourceName, $scope = 0, $resourceInstancePK = null)
	{
		$this->checkInputs( $resourceName, $scope , $resourceInstancePK ); // check if input valid

		$query = ResourcePerm::where('resourceName', '=', $resourceName)->where('scope', '=', $scope);
		if($scope == ResourceConstants::SCOPE_INSTANCE)
		{
			$query = $query->where('resourceInstancePK', '=', $resourceInstancePK);
		}
		return $query->get();	
	}



	/**
	 * Get owner permission for this resource
	 * 
	 * @param string $resourceName 
	 * @param string $resourceInstancePK 
	 * @return ResourcePerm
	 */
	public function getOwnerResourcePerm( $resourceName, $resourceInstancePK )
	{
		$ownerId = $this->roleService->getOwnerRoleId() ;
		return $this->getResourcePerm($ownerId, $resourceName, ResourceConstants::SCOPE_INSTANCE, $resourceInstancePK );
	}

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
	public function addResourcePerm($roleId, $actionName, $resourceName, $scope = 0, $resourceInstancePK = null) 
	{
		$this->checkAllInputsWithActionName($roleId, $actionName, $resourceName, $scope , $resourceInstancePK );
	
		$actionBitwiseValue = $this->resourceActionService->getActionsBitwiseValue($resourceName, array($actionName) ) ;
		return $this->addResourcePerms($roleId, $actionBitwiseValue, $resourceName, $scope , $resourceInstancePK ); 
	}

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
	public function addResourcePermForRoleName($roleName, $actionName, $resourceName, $scope = 0, $resourceInstancePK = null) 
	{
		$roleId = $this->roleService->getRoleId($roleName);
		
		return $this->addResourcePerm($roleId, $actionName, $resourceName, $scope , $resourceInstancePK ); 
	}


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
	public function addResourcePerms($roleId, $actionsBitwiseValue, $resourceName, $scope = 0, $resourceInstancePK = null) 
	{
		$this->checkAllInputs($roleId, $resourceName, $scope , $resourceInstancePK );
		$ownerRoleId = $this->roleService->getOwnerRoleId();

		// if adding permission for owner role, then ownerPK must be provided
		if($roleId == $ownerRoleId )
		{
			throw new \InvalidArgumentException("roleId must not be owner role id. Please use setOwnerResourcePerms methods to add owner resource permission.");
		}

		$entry = $this->getResourcePerm($roleId, $resourceName, $scope , $resourceInstancePK );

		if( is_null($entry) )
		{
			$entry = new ResourcePerm;
			$entry->actionsBitwiseValue = 0;
		}

		$entry->roleId = $roleId;
		$entry->resourceName = $resourceName;
		$entry->scope = $scope;
		
		if($entry->scope == ResourceConstants::SCOPE_INSTANCE)
			$entry->resourceInstancePK = $resourceInstancePK;

		$entry->actionsBitwiseValue = $this->resourceActionService->addPerm($entry->actionsBitwiseValue, $actionsBitwiseValue );
		// if($roleId == $ownerRoleId )
		// 	$entry->ownerPK = $ownerPK ;


		$entry->save();
		return $entry;
	}

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
	public function addResourcePermsForRoleName($roleName, $actionsBitwiseValue, $resourceName, $scope = 0, $resourceInstancePK = null) 
	{
		$roleId = $this->roleService->getRoleId($roleName);
		return $this->addResourcePerms($roleId, $actionsBitwiseValue, $resourceName, $scope , $resourceInstancePK ); 
	}


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
	public function addResourcePermsByActionNames($roleId, array $actionNames, $resourceName, $scope = 0, $resourceInstancePK = null) 
	{
		$actionsBitwiseValue = $this->resourceActionService->getActionsBitwiseValue($resourceName, $actionNames) ;
		return $this->addResourcePerms($roleId, $actionsBitwiseValue, $resourceName, $scope , $resourceInstancePK ); 
	}

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
	public function setResourcePerms($roleId, $actionsBitwiseValue, $resourceName, $scope = 0, $resourceInstancePK = null) {
		$this->checkAllInputs($roleId, $resourceName, $scope , $resourceInstancePK );

		$entry = $this->getResourcePerm($roleId, $resourceName, $scope , $resourceInstancePK );

		if( is_null($entry) )
		{
			$entry = new ResourcePerm;
			$entry->actionsBitwiseValue = 0;
		}

		$entry->roleId = $roleId;
		$entry->resourceName = $resourceName;
		$entry->scope = $scope;
		
		if($entry->scope == ResourceConstants::SCOPE_INSTANCE)
			$entry->resourceInstancePK = $resourceInstancePK;

		$entry->actionsBitwiseValue = $actionsBitwiseValue;
		$entry->save();
		return $entry;
	}

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
	public function setResourcePermsByActionNames($roleId, array $actionNames, $resourceName, $scope = 0, $resourceInstancePK = null) 
	{
		$actionsBitwiseValue = $this->resourceActionService->getActionsBitwiseValue($resourceName, $actionNames) ;
		
		return $this->setResourcePerms($roleId, $actionsBitwiseValue, $resourceName, $scope, $resourceInstancePK );
	}



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
	public function setOwnerResourcePerms($ownerPK, $actionsBitwiseValue, $resourceName, $resourceInstancePK ) 
	{
		$ownerRoleId = $this->roleService->getOwnerRoleId();

		$this->checkAllInputs($ownerRoleId, $resourceName, ResourceConstants::SCOPE_INSTANCE , $resourceInstancePK );

		$entry = $this->getResourcePerm($ownerRoleId, $resourceName, ResourceConstants::SCOPE_INSTANCE , $resourceInstancePK );

		if( is_null($entry) )
		{
			$entry = new ResourcePerm;
			$entry->actionsBitwiseValue = 0;
		}

		$entry->roleId = $ownerRoleId;
		$entry->resourceName = $resourceName;
		$entry->scope = ResourceConstants::SCOPE_INSTANCE;
		$entry->resourceInstancePK = $resourceInstancePK;

		$entry->actionsBitwiseValue = $actionsBitwiseValue;
		$entry->ownerPK = $ownerPK;

		$entry->save();
		return $entry;
	}

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
	public function setOwnerResourcePermsByActionNames($ownerPK, array $actionNames, $resourceName, $resourceInstancePK ) 
	{
		$actionsBitwiseValue = $this->resourceActionService->getActionsBitwiseValue($resourceName, $actionNames) ;
		return $this->setOwnerResourcePerms($ownerPK, $actionsBitwiseValue, $resourceName, $resourceInstancePK ) ;	
	}


	


	/**
	 * Remove all resource permissions at the scope to resources of the type
	 * 
	 * @param string $resourceName 
	 * @param int $scope 
	 * @param string $resourceInstancePK 
	 * @return void
	 */
	public function removeResourcePerms($resourceName, $scope = 0, $resourceInstancePK = null) 
	{
		$this->checkInputs($resourceName, $scope , $resourceInstancePK );

		$query = ResourcePerm::where('resourceName', '=' , $resourceName)->where('scope', '=', $scope);
		if($scope == ResourceConstants::SCOPE_INSTANCE)
			$query = $query->where('resourceInstancePK' , '=' , $resourceInstancePK);

		return $query->delete();
	}



	/**
	 * Returns true if the resource permission grants permission to perform the resource action
	 * 
	 * @param ResourcePerm $resourcePermission 
	 * @param ResourceAction $resourceAction 
	 * @return bool
	 */
	public function hasAction(ResourcePerm $resourcePermission, ResourceAction $resourceAction) 
	{
		if( is_null($resourcePermission) )
			throw new \InvalidArgumentException('resourcePermission cannot be null.');
		if( is_null($resourceAction) )
			throw new \InvalidArgumentException('resourceAction cannot be null.');


		$actionsBitwiseValue = $resourcePermission->actionsBitwiseValue;
		$bitwiseValue = $resourceAction->bitwiseValue;
		return $this->resourceActionService->hasPerm($actionsBitwiseValue, $bitwiseValue);
	}


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
	public function hasPerm(array $roleIdArr, $actionName, $resourceName, $resourceInstancePK = null, $checkOwnerPerm = false, $ownerPK = null )
	{
		if($checkOwnerPerm )
		{
			// verify that resourceInstancePK is provided and ownerPK is provider
			if(empty($resourceInstancePK) )
				throw new \InvalidArgumentException('resourceInstancePK must be provided.');

			if(empty($ownerPK) )
				throw new \InvalidArgumentException('ownerPK must be provided.');

		}

		return $this->hasScopePerms( $roleIdArr, $actionName, $resourceName, 
					array( ResourceConstants::SCOPE_ALL, ResourceConstants::SCOPE_INSTANCE) , 
					array( null, $resourceInstancePK), $checkOwnerPerm , $ownerPK );
	}


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
	public function hasScopePerm(array $roleIdArr, $actionName, $resourceName, $scope , $resourceInstancePK)
	{
		$this->checkInputs($resourceName, $scope , $resourceInstancePK );
		return $this->hasScopePerms( $roleIdArr, $actionName, $resourceName, array($scope) , array($resourceInstancePK));
	}


	/**
	 * Return true of given roles have access to a resource based on a list of scopes.
	 * if $checkOwnerPerm is true, ownerPK must be provided. This is to verify that the user has the owner permission.
	 * This method cater for future enhancements when it needs to have more fine-grained permission hierarchy (more scopes than the default 2)
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
	public function hasScopePerms(array $roleIdArr, $actionName, $resourceName, array $scopeArr , array $resourceInstancePKArr, $checkOwnerPerm = false, $ownerPK = null)
	{
		if(empty($resourceName) )
			throw new \InvalidArgumentException('Resource name must not be empty.');

		if(empty($actionName) )
			throw new \InvalidArgumentException('Action name must not be empty.');

		if(count($scopeArr) != count($resourceInstancePKArr))
			throw new \InvalidArgumentException('Number of items in scope array must be the same as number of resourceInstancePK');

		$ownerRoleId = $this->roleService->getOwnerRoleId();
		$actionBitwiseValue = $this->resourceActionService->getAction($resourceName, $actionName)->bitwiseValue ;

		$tableName = ConfigHelper::getResourcePermTableName();

		if($checkOwnerPerm) // add owner role to array if not exist
		{
			if(!in_array($ownerRoleId, $roleIdArr))
				$roleIdArr[] = $ownerRoleId;
		}
		else{
			$index = array_search($ownerRoleId, $roleIdArr);
			if($index !== false)
			{
				// remove owner role
				unset($roleIdArr[$index]);
			}
		}

		$query = DB::table($tableName)->where('resourceName', '=' , $resourceName)->whereIn('roleId', $roleIdArr ) ;
		
		// must pass the parameters inline.. because of a bug in sqlite for query builder for int parameter building
		$query = $query->whereRaw("( actionsBitwiseValue & $actionBitwiseValue = $actionBitwiseValue )"  ) ;
		
		$query = $query->where(
							function($q) use ( $ownerRoleId , $scopeArr, $resourceInstancePKArr, $checkOwnerPerm, $ownerPK)
				            {
				                
				            	if($checkOwnerPerm)
				            	{
				            		$q = $q->orWhereRaw(" (ownerPK = ? AND roleId = ?) ", array($ownerPK, $ownerRoleId) );
				            	}

				            	$numOfScopes = count($scopeArr);
				            	for($i = 0; $i < $numOfScopes ; $i++)
				            	{
				            		$scope = $scopeArr[$i];
				            		$resourceInstancePK = $resourceInstancePKArr[$i];

				            		if($scope == ResourceConstants::SCOPE_ALL)
				            			$q = $q->orWhereRaw("scope = " . ResourceConstants::SCOPE_ALL);
				            		else
				            		{
				            			if( !is_null($resourceInstancePK) )
				            				$q = $q->orWhereRaw(" (scope = ? AND resourceInstancePK = ? )", array($scope , $resourceInstancePK) ) ;
				            		}
				            			
				            	}
				            	

				            }
				         );

		
		// log query testing
		// $this->logQuery();

		$count = $query->count();
		
		return ($count > 0);
	}

	// for debugging only
	private function logQuery(){
		\Illuminate\Support\Facades\Event::listen('illuminate.query', function($query, $bindings, $time, $name) 
		{ 
	
		    $data = compact('bindings', 'time', 'name');

        	// Format binding data for sql insertion
	        foreach ($bindings as $i => $binding)
	        {   
	            if ($binding instanceof \DateTime)
	            {   
	                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
	            }
	            else if (is_string($binding))
	            {   
	                $bindings[$i] = "'$binding'";
	            }   
	        }       

	        // Insert bindings into query
	        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
	        $query = vsprintf($query, $bindings); 

	        Log::info($query, $data);
		});
	}


}