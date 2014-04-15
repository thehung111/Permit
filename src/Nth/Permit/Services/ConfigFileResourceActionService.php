<?php

namespace Nth\Permit\Services;

use Nth\Permit\Interfaces\ResourceActionServiceInterface;
use Nth\Permit\Exceptions\NotImplementedException;
use Nth\Permit\Helper\ConfigHelper;


/**
 * This class will load resource action information from config file : perms.php 
 * 
 * @package Nth\Permit\Services
 */
class ConfigFileResourceActionService extends \Nth\Permit\Services\AbstractBitwiseResourceActionService
{

	/**
	 * return action based on resource name and action name.
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return ResourceAction|null
	 */
	public function getAction($resourceName, $actionName)
	{
		if(empty($resourceName) || empty($actionName) )
			throw new \InvalidArgumentException('Resource name and action name must not be empty.');

		$entry = null;
		$supported_actions = $this->getSupportedActions($resourceName);
		
		foreach ($supported_actions as $action) {
			if(!array_key_exists(ResourceActionServiceInterface::ACTION_NAME, $action))
			{
				throw new \Nth\Permit\Exceptions\InvalidResourceActionException('Invalid action. Missing actionName property.');
			}	

			if($action[ResourceActionServiceInterface::ACTION_NAME] === $actionName )
			{
				$entry = new \Nth\Permit\Models\ResourceAction;
				$entry->resourceName = $resourceName;
				$entry->actionName = $actionName;
				$entry->bitwiseValue = intval( $action[ResourceActionServiceInterface::BITWISE_VALUE] );

				break;
			}
		}

		return $entry;
		
	}



	/**
	 * return whether the action name string is valid and supported for this resource (based on resource name)
	 * return true if valid
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return bool
	 */
	public function isActionExists($resourceName, $actionName)
	{
		if(empty($resourceName) || empty($actionName) )
			throw new \InvalidArgumentException('Resource name and action name must not be empty.');

		$supported_actions = $this->getSupportedActions($resourceName);
		$found = false;
		foreach ($supported_actions as $action) {
			if(!array_key_exists(ResourceActionServiceInterface::ACTION_NAME, $action))
			{
				throw new \Nth\Permit\Exceptions\InvalidResourceActionException('Invalid action. Missing actionName property.');
			}	

			if($action[ResourceActionServiceInterface::ACTION_NAME] === $actionName )
			{
				$found = true;
				break;
			}
		}

		return $found ;
	}

	
	/**
	 * Return array of supported resource actions for this resource
	 * If resourceName cannot be found, just return an empty array
	 * 
	 * @param string $resourceName 
	 * @return array
	 */
	public function getSupportedActions($resourceName)
	{
		if(empty($resourceName)  )
			throw new \InvalidArgumentException('Resource name must not be empty.');

		// load permission resources file from config file
		$init_perms_arr = ConfigHelper::getInitPermResources();
		if(array_key_exists($resourceName, $init_perms_arr  ))
		{
			$supported_actions = $init_perms_arr[$resourceName] ;
			return $supported_actions;
		}

		return array();
	}


	/**
	 * will throw NotSupportedException 
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @param void $actionBitwiseValue 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function addAction($resourceName, $actionName , $actionBitwiseValue )
	{
		throw new \Nth\Permit\Exceptions\NotSupportedException;
	}


	/**
	 * will throw NotSupportedException 
	 * 
	 * @param string $resourceName 
	 * @param array $actionArray 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function addActions($resourceName, array $actionArray )
	{
		throw new \Nth\Permit\Exceptions\NotSupportedException;
	}


	/**
	 * will throw NotSupportedException 
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function removeAction($resourceName, $actionName )
	{
		throw new \Nth\Permit\Exceptions\NotSupportedException;
	}


	/**
	 * will throw NotSupportedException* 
	 * @param string $resourceName 
	 * @param array $actionArray 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function removeActions($resourceName, array $actionArray )
	{
		throw new \Nth\Permit\Exceptions\NotSupportedException;
	}

	/**
	 * will throw NotSupportedException
	 * 
	 * @param string $resourceName 
	 * @param array $actionArray 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function setActions($resourceName, array $actionArray )
	{
		throw new \Nth\Permit\Exceptions\NotSupportedException;
	}


	/**
	 * Remove all actions for this resource. Not supported if data provider is config file
	 * 
	 * @param string $resourceName 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function removeAllActions($resourceName )
	{
		throw new \Nth\Permit\Exceptions\NotSupportedException;
	}

}
