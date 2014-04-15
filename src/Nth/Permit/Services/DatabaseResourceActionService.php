<?php

namespace Nth\Permit\Services;

use Nth\Permit\Interfaces\ResourceActionServiceInterface;
use Nth\Permit\Exceptions\NotImplementedException;
use Nth\Permit\Helper\ConfigHelper;
use Nth\Permit\Models\ResourceAction;
use Illuminate\Support\Facades\DB;

/**
 * This class will load resource action information from config file : perms.php 
 * 
 * @package Nth\Permit\Services
 */
class DatabaseResourceActionService extends \Nth\Permit\Services\AbstractBitwiseResourceActionService
{

	/**
	 * return true if there is a resource with name = resourceName and 
	 * the resource has either the same actionName or the same bitWiseValue
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @param int $bitwiseValue 
	 * @return bool
	 */
	public function isActionExistsWithNameOrBitwiseValue($resourceName, $actionName ,  $bitwiseValue)
	{
		return (ResourceAction::whereRaw('resourceName = ? AND (actionName = ? OR bitwiseValue = ?)', array($resourceName, $actionName,  $bitwiseValue) )->count() > 0);
	}

	/**
	 * Get ResourceAction by resource name and action name
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return ResourceAction
	 */
	public function getActionByNames($resourceName, $actionName)
	{
		return ResourceAction::whereRaw('resourceName = ? AND actionName = ?', array($resourceName, $actionName))->first();
	}

	/**
	 * Get ResourceAction by resource name and action name
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return ResourceAction
	 */
	public function getActionByBitwiseValue($resourceName, $bitwiseValue)
	{
		return ResourceAction::whereRaw('resourceName = ? AND bitwiseValue = ?', array($resourceName, $bitwiseValue))->first();
	}

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

		return ResourceAction::whereRaw('resourceName = ? AND actionName = ?', array($resourceName, $actionName))->first();
	
	}

	/**
	 * return whether the action name string is valid i.e. exist in DB and supported for this resource (based on resource name)
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

		$action = $this->getActionByNames($resourceName, $actionName);

		return (!is_null($action)) ;
		
		
	}

	
	/**
	 * Return list of supported actions for this resource
	 * 
	 * @param string $resourceName 
	 * @return array
	 */
	public function getSupportedActions($resourceName)
	{
		if(empty($resourceName)  )
			throw new \InvalidArgumentException('Resource name must not be empty.');


		return ResourceAction::where('resourceName', $resourceName)->get()->toArray();
	}


	/**
	 * Add an action for the resource. 
	 * If this actionName already exists or actionBitwiseValue already exist for this resourceName
	 * the action will not be added.
	 * 
	 * Return true if action is added successfully.
	 * Return false if action already exists or fail to add
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @param void $actionBitwiseValue 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function addAction($resourceName, $actionName , $actionBitwiseValue )
	{
		if(empty($resourceName) || empty($actionName) )
			throw new \InvalidArgumentException('Resource name and action name must not be empty.');

		// record already exist
		if($this->isActionExistsWithNameOrBitwiseValue($resourceName, $actionName ,  $actionBitwiseValue) )
			return false;

		$this->checkBitwiseValue($actionBitwiseValue);

		$action = new ResourceAction;
		$action->resourceName = $resourceName;
		$action->actionName   = $actionName;
		$action->bitwiseValue = $actionBitwiseValue;
		return $action->save();
			
	}


	/**
	 * Add actions for the resource. 
	 * For each action in $actionArray, 
	 * if this actionName already exists or actionBitwiseValue already exist for this resourceName,
	 * then action will not be added.
	 * 
	 * $actionArray example: array(
	 *		array('actionName' => 'post.add'		, 'bitwiseValue' => 1),
	 *		array('actionName' => 'post.edit'		, 'bitwiseValue' => 2),
	 *		array('actionName' => 'post.delete'		, 'bitwiseValue' => 4),
	 *		array('actionName' => 'post.view'		, 'bitwiseValue' => 8),
	 *	)
	 * Return true if actions are added successfully.
	 * 
	 * @param string $resourceName 
	 * @param array $actionArray 
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function addActions($resourceName, array $actionArray )
	{
		if(empty($resourceName) )
			throw new \InvalidArgumentException('Resource name must not be empty.');

		$supportedActions = $this->getSupportedActions($resourceName);

		$resultArr = array();
		foreach ($actionArray as $action) {
			$actionName = $action[ResourceActionServiceInterface::ACTION_NAME];
			$bitwiseValue = $action[ResourceActionServiceInterface::BITWISE_VALUE];
			
			if(empty($actionName) )
				throw new \InvalidArgumentException('Action name must not be empty.');

			$this->checkBitwiseValue($bitwiseValue);

			$exist = false;
			foreach($supportedActions as $existAction)
			{
				if($existAction['actionName'] == $actionName  ||  $existAction['bitwiseValue'] == $bitwiseValue )
				{
					$exist = true;
					break;
				}
			}

			// record already exist
			if($exist)
				continue;

			$resultArr[] =  array( 'resourceName' => $resourceName , 'actionName' => $actionName, 'bitwiseValue' => $bitwiseValue); 
		}

		if(count($resultArr ) > 0)
		{
			DB::table(ConfigHelper::getResourceActionTableName())->insert($resultArr);
			return true;
		}

		return false;

	}


	/**
	 * Remove an action for the resource. 
	 * Return true if action is removed successfully.
	 * Return false if action does not exist
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return bool
	 */
	public function removeAction($resourceName, $actionName )
	{
		if(empty($resourceName) || empty($actionName) )
			throw new \InvalidArgumentException('Resource name and action name must not be empty.');

		$action = $this->getActionByNames($resourceName, $actionName);

		if(is_null($action))
			return false;
		else
		{
			$action->delete();
			return true;
		}
	}


	/**
	 * Remove actions for the resource. 
	 * Return true if actions are removed successfully.
	 * $actionArray example: array('post.add', 'post.edit')
	 * 
	 * @param string $resourceName 
	 * @param array of string $actionArray 
	 * @return bool
	 */
	public function removeActions($resourceName, array $actionArray )
	{
		if(empty($resourceName)  )
			throw new \InvalidArgumentException('Resource name must not be empty.');

		

		if(!empty($actionArray ))
		{
			$where_sql = "resourceName = ? AND " ;
			$valueArr = array($resourceName);

			$queryArr = array();
			foreach ($actionArray as $action) {
				$queryArr[] = " actionName = ? " ;
				$valueArr[] = $action;
			}
			$where_sql .= implode("OR", $queryArr);

			DB::table(ConfigHelper::getResourceActionTableName())->whereRaw($where_sql ,$valueArr )->delete() ;
			return true;
		}

		return false;
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
		if(empty($resourceName)  )
			throw new \InvalidArgumentException('Resource name must not be empty.');

		return DB::table(ConfigHelper::getResourceActionTableName())->where( 'resourceName', $resourceName)->delete();		
	}

	/**
	 * Set actions for the resource. This method will first remove all actions for this resourceName, then re-add them 
	 * Return true if all actions are added successfully.
	 * 
	 * $actionArray example: 
	 *  array(
	 *		array('actionName' => 'post.add'		, 'bitwiseValue' => 1),
	 *		array('actionName' => 'post.edit'		, 'bitwiseValue' => 2),
	 *		array('actionName' => 'post.delete'		, 'bitwiseValue' => 4),
	 *		array('actionName' => 'post.view'		, 'bitwiseValue' => 8),
	 *	)
	 * 
	 * @param string $resourceName 
	 * @param array $actionArray 
	 * @return bool
	 */
	public function setActions($resourceName, array $actionArray )
	{
		if(empty($resourceName)  )
			throw new \InvalidArgumentException('Resource name must not be empty.');


		$this->removeAllActions($resourceName );
		return $this->addActions($resourceName, $actionArray );
	}

}
