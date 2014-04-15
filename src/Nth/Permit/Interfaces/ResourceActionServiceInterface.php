<?php

namespace Nth\Permit\Interfaces;

use Nth\Permit\Exceptions\NoSuchResourceActionException;

interface ResourceActionServiceInterface{

	/**
	 * actionName property name for a ResourceAction entry
	 */
	const ACTION_NAME = 'actionName' 	 ;

	/**
	 * bitwise property for a ResourceAction entry
	 */
	const BITWISE_VALUE = 'bitwiseValue' ;

	/**
	 * return action based on resource name and action name.
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return ResourceAction|null
	 */
	public function getAction($resourceName, $actionName);



	/**
	 * return whether the action name string is valid and supported for this resource (based on resource name)
	 * return true if actionName exists and can be found from data provider
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return bool
	 */
	public function isActionExists($resourceName, $actionName);


	/**
	 * Check whether the action name string is valid and supported for this resource (based on resource name)
	 * throw exception if it is not
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return void
	 * @throws \Nth\Permit\Exceptions\NoSuchResourceActionException
	 */
	public function checkActionExists($resourceName, $actionName);


	/**
	 * Return list of supported actions for this resource
	 * Example return result:
	 * array(
	 *		array('actionName' => 'user.add'		, 'bitwiseValue' => 1),
	 *		array('actionName' => 'user.edit'		, 'bitwiseValue' => 2),
	 *		array('actionName' => 'user.delete'		, 'bitwiseValue' => 4),
	 *		array('actionName' => 'user.view'		, 'bitwiseValue' => 8),
	 *	)
	 * 
	 * @param string $resourceName 
	 * @return array
	 */
	public function getSupportedActions($resourceName);


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
	public function getActionNames($resourceName, $actionsBitwiseValue) ;


	/**
	 * return the bitwise value for list of action names
	 * 
	 * @param string $resourceName 
	 * @param array $actionNames 
	 * @return int
	 */
	public function getActionsBitwiseValue($resourceName, array $actionNames) ;



	/**
	 * check if this actionsBitwiseValue contains the permission actionBitwiseValue
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param int $actionBitwiseValue 
	 * @return bool
	 */
	public function hasPerm($actionsBitwiseValue, $actionBitwiseValue);

	/**
	 * check if this actionsBitwiseValue contains all permission specified in $actionBitwiseValueArray
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param array of int $actionBitwiseValueArray 
	 * @return bool
	 */
	public function hasPerms($actionsBitwiseValue, array $actionBitwiseValueArray = array() );


	/**
	 * add the perrmission actionBitwiseValue to actionsBitwiseValue
	 * return the updated value
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param int $actionBitwiseValue 
	 * @return int
	 */
	public function addPerm($actionsBitwiseValue, $actionBitwiseValue );



	/**
	 * add permissions in actionBitwiseValueArray to actionsBitwiseValue
	 * return updated value
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param array of int $actionBitwiseValueArray 
	 * @return bool
	 */
	public function addPerms($actionsBitwiseValue, array $actionBitwiseValueArray = array() );

	/**
	 * remove the perrmission actionBitwiseValue from actionsBitwiseValue
	 * return the updated value
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param int $actionBitwiseValue 
	 * @return int
	 */
	public function removePerm($actionsBitwiseValue, $actionBitwiseValue );



	/**
	 * remove permissions in actionBitwiseValueArray from actionsBitwiseValue
	 * return updated value
	 * 
	 * @param int $actionsBitwiseValue 
	 * @param array of int $actionBitwiseValueArray 
	 * @return bool
	 */
	public function removePerms($actionsBitwiseValue, array $actionBitwiseValueArray = array() );


	/**
	 * Add an action for the resource. 
	 * Note that it is not possible to add a resource action if the data provider is config file i.e. perms.php.
	 * This method will throw NotSupportedException if it is not applicable.
	 * Return true if action is added successfully.
	 * Return false if action already exists
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @param void $actionBitwiseValue 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function addAction($resourceName, $actionName , $actionBitwiseValue );


	/**
	 * Add actions for the resource. 
	 * Note that it is not possible to add a resource action if the data provider is config file i.e. perms.php.
	 * This method will throw NotSupportedException if it is not applicable.
	 * Return true if actions are added successfully.
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
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function addActions($resourceName, array $actionArray );


	/**
	 * Remove an action for the resource. 
	 * Note that it is not possible to remove a resource action if the data provider is config file i.e. perms.php.
	 * This method will throw NotSupportedException if it is not applicable.
	 * Return true if action is removed successfully.
	 * Return false if action does not exist
	 * 
	 * @param string $resourceName 
	 * @param string $actionName 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function removeAction($resourceName, $actionName );


	/**
	 * Remove actions for the resource. 
	 * Note that it is not possible to remove a resource action if the data provider is config file i.e. perms.php.
	 * This method will throw NotSupportedException if it is not applicable.
	 * Return true if actions are removed successfully.
	 * $actionArray example: array('post.add', 'post.edit')
	 * 
	 * @param string $resourceName 
	 * @param array of string $actionArray 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function removeActions($resourceName, array $actionArray );


	/**
	 * Remove all actions for this resource. Not supported if data provider is config file
	 * 
	 * @param string $resourceName 
	 * @return bool
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function removeAllActions($resourceName );


	/**
	 * Set actions for the resource. This method will first remove all actions for this resourceName, then re-add them 
	 * Note that it is not possible to add a resource action if the data provider is config file i.e. perms.php.
	 * This method will throw NotSupportedException if it is not applicable.
	 * Return true if actions are added successfully.
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
	 * @throws \Nth\Permit\Exceptions\NotSupportedException
	 */
	public function setActions($resourceName, array $actionArray );


	/**
	 * check if input is int and is power of 2
	 * 
	 * @param int $actionBitwiseValue 
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function checkBitwiseValue($actionBitwiseValue);

}