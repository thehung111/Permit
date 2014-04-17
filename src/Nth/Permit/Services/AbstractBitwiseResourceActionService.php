<?php

namespace Nth\Permit\Services;

use Nth\Permit\Interfaces\ResourceActionServiceInterface;
use Nth\Permit\Exceptions\NotImplementedException;
use Nth\Permit\Helper\ConfigHelper;


/**
 * Abstract class that implements all common bitwise operations for add/remove permission 
 * 
 * @package Nth\Permit\Services
 */
abstract class AbstractBitwiseResourceActionService implements ResourceActionServiceInterface
{

	/**
	 * Check whether the action name string is valid and supported for this resource (based on resource name)
	 * throw exception if it is not
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return void
	 */
	public function checkActionExists($resourceName, $actionName)
	{
		if(!$this->isActionExists($resourceName, $actionName))
			throw new \Nth\Permit\Exceptions\NoSuchResourceActionException;
	}




	/**
	 * Return bitwise representation for permission array i.e. do a simple OR afterlooping
	 * 
	 * @param  array $actionBitwiseValueArray 
	 * @return int
	 */
	protected function getPermBitwiseValueFromArray(array $actionBitwiseValueArray = array())
	{
		$perms = array_reduce($actionBitwiseValueArray, function($carry, $item){
			if(!is_int($item))
				throw new \InvalidArgumentException("actionBitwiseValue must be integer: $item");

			return ($carry | $item);
		}, 0);

		return $perms;
	}

	/**
	 * check if this actionsBitwiseValue contains the permission actionBitwiseValue
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param int $actionBitwiseValue 
	 * @return bool
	 */
	public function hasPerm($actionsBitwiseValue, $actionBitwiseValue)
	{
		if( (!is_int($actionBitwiseValue)) || (!is_int($actionsBitwiseValue))   )
			throw new \InvalidArgumentException("Input must be integer.  actionsBitwiseValue: $actionsBitwiseValue, actionBitwiseValue: $actionBitwiseValue ");
			

		return (($actionsBitwiseValue & $actionBitwiseValue) === $actionBitwiseValue );
	}

	/**
	 * check if this actionsBitwiseValue contains all permission specified in $actionBitwiseValueArray
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param array of int $actionBitwiseValueArray 
	 * @return bool
	 */
	public function hasPerms($actionsBitwiseValue, array $actionBitwiseValueArray = array() )
	{
		if(!is_int($actionsBitwiseValue))
			throw new \InvalidArgumentException("actionsBitwiseValue must be integer: $actionsBitwiseValue");

		$perms = $this->getPermBitwiseValueFromArray($actionBitwiseValueArray);

		return (($actionsBitwiseValue & $perms) === $perms );
		
	}


	/**
	 * add the perrmission actionBitwiseValue to actionsBitwiseValue
	 * return the updated value
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param int $actionBitwiseValue 
	 * @return int
	 */
	public function addPerm($actionsBitwiseValue, $actionBitwiseValue )
	{
		if( (!is_int($actionBitwiseValue)) || (!is_int($actionsBitwiseValue))   )
			throw new \InvalidArgumentException("Input must be integer. actionsBitwiseValue: $actionsBitwiseValue, actionBitwiseValue: $actionBitwiseValue");
		
		return ($actionsBitwiseValue | $actionBitwiseValue );
	}



	/**
	 * add permissions in actionBitwiseValueArray to actionsBitwiseValue
	 * return updated value
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param array of int $actionBitwiseValueArray 
	 * @return bool
	 */
	public function addPerms($actionsBitwiseValue, array $actionBitwiseValueArray = array() )
	{
		$perms = $this->getPermBitwiseValueFromArray($actionBitwiseValueArray);
		return ($actionsBitwiseValue | $perms );
	}

	/**
	 * remove the perrmission actionBitwiseValue from actionsBitwiseValue
	 * return the updated value
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param int $actionBitwiseValue 
	 * @return int
	 */
	public function removePerm($actionsBitwiseValue, $actionBitwiseValue )
	{
		if( (!is_int($actionBitwiseValue)) || (!is_int($actionsBitwiseValue))   )
			throw new \InvalidArgumentException("Input must be integer. actionsBitwiseValue: $actionsBitwiseValue, actionBitwiseValue: $actionBitwiseValue");
		
		
		return ($actionsBitwiseValue & (~$actionBitwiseValue) );
	}


	/**
	 * remove permissions in actionBitwiseValueArray from actionsBitwiseValue
	 * return updated value
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param array of int $actionBitwiseValueArray 
	 * @return bool
	 */
	public function removePerms($actionsBitwiseValue, array $actionBitwiseValueArray = array() )
	{
		$perms = $this->getPermBitwiseValueFromArray($actionBitwiseValueArray);
		return ($actionsBitwiseValue & (~$perms) );
	}

	/**
	 * Convert actionsBitwiseValue int value into list of names for each of the allowed action in supported actions
	 * actionsBitwiseValue store all the permissions for a particular resource after applying bitwise operation
	 * e.g. 
	 * Assume CRUD actions corresponds to 1111 in actionsBitwiseValue
	 * If actionsBitwiseValue = 0101 ==> return array('read', 'delete')
	 * 
	 * @param string $resourceName 
	 * @param int $actionsBitwiseValue 
	 * @return array
	 */
	public function getActionNames($resourceName, $actionsBitwiseValue) 
	{
		if(empty($resourceName)  )
			throw new \InvalidArgumentException('Resource name must not be empty.');

		$supported_actions = $this->getSupportedActions($resourceName);
		
		$results = array();

		foreach ($supported_actions as $action) {
			if(!array_key_exists(ResourceActionServiceInterface::ACTION_NAME, $action))
			{
				throw new \Nth\Permit\Exceptions\InvalidResourceActionException('Invalid action. Missing actionName property.');
			}	

			if(!array_key_exists(ResourceActionServiceInterface::BITWISE_VALUE, $action))
			{
				throw new \Nth\Permit\Exceptions\InvalidResourceActionException('Invalid action. Missing bitwiseValue property.');
			}

			if($this->hasPerm($actionsBitwiseValue, $action[ResourceActionServiceInterface::BITWISE_VALUE])){
				$results[] = $action[ResourceActionServiceInterface::ACTION_NAME];
			}			
			
		}

		return $results;
		
	}


	/**
	 * return the bitwise value for list of action names
	 * 
	 * @param string $resourceName 
	 * @param array $actionNames 
	 * @return int
	 */
	public function getActionsBitwiseValue($resourceName, array $actionNames)
	{
		if(empty($resourceName)  )
			throw new \InvalidArgumentException('Resource name must not be empty.');

		$supported_actions = $this->getSupportedActions($resourceName);
		
		$result = 0;

		foreach ($supported_actions as $action) {
			if(!array_key_exists(ResourceActionServiceInterface::ACTION_NAME, $action))
			{
				throw new \Nth\Permit\Exceptions\InvalidResourceActionException('Invalid action. Missing actionName property.');
			}

			if(in_array($action[ResourceActionServiceInterface::ACTION_NAME], $actionNames))
			{
				$result  |= $action[ResourceActionServiceInterface::BITWISE_VALUE] ;
			}
			
		}

		return $result;

	}

	/**
	 * check if input is int and is power of 2
	 * 
	 * @param int $actionBitwiseValue 
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function checkBitwiseValue($actionBitwiseValue)
	{
		if(is_int($actionBitwiseValue))
		{
			if($actionBitwiseValue & ($actionBitwiseValue - 1) != 0 )
			{
				throw new \InvalidArgumentException("actionBitwiseValue must be power of 2: $actionBitwiseValue");
			}
				

		}
		else{
			throw new \InvalidArgumentException("actionBitwiseValue must be int: $actionBitwiseValue");
		}
		
		
	}
	
}