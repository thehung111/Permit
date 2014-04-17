<?php

namespace Nth\Permit\Helper;

use Illuminate\Support\Facades\Config;

/**
 * Load Configuration
 */ 
class ConfigHelper{
	
	const db_prefix_config_key = "permit::db_prefix" ;
	const user_role_table_config_key = "permit::user_role_table_name" ;
	const role_table_config_key = "permit::role_table_name" ;
	const resource_action_table_config_key = "permit::resource_action_table_name" ;
	const resource_perm_table_config_key = "permit::resource_perm_table_name" ;
	const init_perm_resource_config_key = "permit::perms.initial_perm_resources" ;
	const table_prefix_separator = "_" ;
	const data_provider_config_key = "permit::action_data_provider" ;
	const user_pk_column_name_config_key = "permit::user_pk_column_name" ;
	const user_pk_column_data_type_config_key = "permit::user_pk_column_data_type" ;



	protected static $db_prefix = null;
	protected static $user_role_table_name = null;
	protected static $role_table_name = null;
	protected static $resource_action_table_name = null;
	protected static $resource_perm_table_name = null;
	protected static $perm_resources_map = null;
	protected static $data_provider = null;
	protected static $user_pk_column_name = null;
	protected static $user_pk_column_data_type = null;


	public static function getUserPKName()
	{
		if(is_null(static::$user_pk_column_name ))
		{
			static::$user_pk_column_name = Config::get(self::user_pk_column_name_config_key);
		}
		return static::$user_pk_column_name;
	}


	public static function getUserPKDataType()
	{
		if(is_null(static::$user_pk_column_data_type ))
		{
			static::$user_pk_column_data_type = Config::get(self::user_pk_column_data_type_config_key);
		}
		return static::$user_pk_column_data_type;
	}


	/**
	 * Get data provider string
	 * 
	 * @return string
	 */
	public static function getDataProvider()
	{
		if(is_null(static::$data_provider ))
		{
			static::$data_provider = Config::get(self::data_provider_config_key);
		}
		return static::$data_provider;
		
	}

	/**
	 * Get initial permission resources and supported actions from config file
	 * 
	 * @return array
	 */
	public static function getInitPermResources()
	{
		if(empty(static::$perm_resources_map ))
		{
			static::$perm_resources_map = Config::get(self::init_perm_resource_config_key) ;
		}
		return static::$perm_resources_map;
	}

	public static function setPermResourcesMap(array $arr)
	{
		static::$perm_resources_map = $arr;
	}

	/**
	 * Get database table prefix for this package to prevent collision 
	 * 
	 * @return string
	 */
	public static function getDBPrefix()
	{
		if(is_null(static::$db_prefix ))
		{
			static::$db_prefix = Config::get(self::db_prefix_config_key);
		}
		return static::$db_prefix;
		
	}

	public static function setDBPrefix($prefix)
	{
		static::$db_prefix = $prefix;
	}

	/**
	 * If there is a prefix in config, return the name with prefix in the form of prefix_name
	 * If prefix is not defined then return name
	 * 
	 * @param string $name Name of a table in database 
	 * @return string
	 */
	public static function getDBTableName($name)
	{
		$prefix = static::getDBPrefix();

		return (empty($prefix) ? $name : ( $prefix . self::table_prefix_separator .  $name) );
	}

	/**
	 * Get name of the role table
	 * 
	 * @return string 
	 */
	public static function getUserRoleTableName()
	{
		if(is_null(static::$user_role_table_name ))
		{
			$store = Config::get(self::user_role_table_config_key);
			static::$user_role_table_name = static::getDBTableName($store); 
		}
		return static::$user_role_table_name;
	}

	/**
	 * Get name of the role table
	 * 
	 * @return string 
	 */
	public static function getRoleTableName()
	{
		if(is_null(static::$role_table_name ))
		{
			$store = Config::get(self::role_table_config_key);
			static::$role_table_name = static::getDBTableName($store); 
		}
		return static::$role_table_name;
	}

	/**
	 * Get name of the resource table
	 * 
	 * @return string 
	 */
	public static function getResourceActionTableName()
	{
		if(is_null(static::$resource_action_table_name ))
		{
			$store = Config::get(self::resource_action_table_config_key);
			static::$resource_action_table_name = static::getDBTableName($store); 
		}
		return static::$resource_action_table_name;
		
	}

	/**
	 * Get name of the resource permission table
	 * 
	 * @return string
	 */
	public static function getResourcePermTableName()
	{
		if(is_null(static::$resource_perm_table_name ))
		{
			$store = Config::get(self::resource_perm_table_config_key);
			static::$resource_perm_table_name = static::getDBTableName($store); 
		}
		return static::$resource_perm_table_name;
	}

}