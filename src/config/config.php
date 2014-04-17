<?php

/**
 * Configuration
 */ 
return array(

	/**
	 *  The name of user column in User table
	 * 	This setting allow migration to create the user-role relationship table based on the primary key of user table 
	 */
	'user_pk_column_name' => 'userId',

	/*
	|--------------------------------------------------------------------------
	| Data type for user primary key column
	|--------------------------------------------------------------------------
	|
	| 	This option allows you to specify the data type of user pk column
	|
	| 	Supported options: "string", "integer", "bigInteger", "integer_unsigned", "bigInteger_unsigned"
	|	If no option is provided, integer_unsigned will be used
	|
	*/
	'user_pk_column_data_type' => 'integer_unsigned',


	// prefix for database tables to prevent conflict with other packages
	'db_prefix' => 'nth', 
	
	/*
	|--------------------------------------------------------------------------
	| Database tables
	|--------------------------------------------------------------------------
	|	Configure the table names if necessary.
	| 	If database prefix is set, the prefix will be appended to the table name
	|	e.g. if role_table_name is 'role' and db_prefix is 'nth', then table name for role is nth_role
	|
	*/
	'user_role_table_name' => 'user_role',
	'role_table_name' => 'role',
	'resource_action_table_name' => 'resource_action',
	'resource_perm_table_name' => 'resource_perm' , // resource permission

	/*
	|--------------------------------------------------------------------------
	| Data Provider
	|--------------------------------------------------------------------------
	|
	| 	This option allows you to specify where to load resource actions data from
	|	"config_file" options will load the data from local config file : perms.php 
	|	"database" file will load the data from database table: resource_action
	| 	
	|	Supported options: "config_file", "database"
	|
	*/
	'action_data_provider' => 'config_file' ,
	
);