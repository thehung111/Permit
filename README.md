Permit
======

A Laravel 4.x package that add Role Based Access Control (RBAC) using bitwise operations. This package borrows the idea from Liferay permission algorithm 6.

Reference: [Liferay Permission Optimization Discussion](http://www.liferay.com/web/guest/community/forums/-/message_boards/message/2380562)

Installation
============

1) Under `require` section of composer.json, add reference to permit package:

```
"require": {
		"laravel/framework": "4.1.*",
		"nth/permit": "dev-master"  // add this line
	},
```

2) Run update command:

```
$ composer update
```

Configuration
=============

## Register Provider & Alias 
Update `app/config/app.php` to register provider and alias: 

```
'providers' => array(
		....
		// register provider 
		'Nth\Permit\PermitServiceProvider', 
		),

'aliases' => array(
        ....
        
		// register alias for permit facade
		'Permit' => 'Nth\Permit\Facades\PermitFacade',  
		
	),
		
```

## Publish configuration

Run the below command to copy the default config from Permit package to main app:
```
$ php artisan config:publish nth/permit 
```

The configuration options in `config.php` are as below. Database table names can be configured.

```
<?php

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
```

Note that `Resource Actions` data can be loaded from local file (`perms.php`) or the database table. 

## Create Initial Database Tables & Data

1) First run migration to create the tables:

```
$ php artisan migrate --package="nth/permit"
```

2) Run database seeding:
```
$ php artisan db:seed --class="\Nth\Permit\Seeds\PermitDatabaseSeeder"
```

### How It Works?

TODO: put in the schema and explain various concepts.

The following tables are created:

- `role`
- `user_role` (the userid column datatype is mixed based on config file)
- `resource_action` (an action user can perform on a particular resource e.g. edit a blog post)
- `resource_perm` (the actual table that stores the permissions)

A resource represents a connection to an entity you want to perform the action. While a resource action describes what you want to do to the entity.

E.g. Resource is "blog", resource actions can be "blog.add", "blog.edit", "blog.delete".

A resource permission consists of the following fields:

- `resourceName` (e.g. 'patient')

- `roleId` (e.g. 'doctor' who can view this particular 'patient' only if the patient is assigned to him. Or a 'patient' who can only view his own record)

- `actionsBitwiseValue` : the sum of all actions this role can do i.e a AND bit operation for all the resource action bitwise value

- `scope` : refers to whether this permission applies to all instances or only to individual instances (e.g. can a 'doctor' role view all patient records or only a particular record of his patient. Corresponds to `SCOPE_ALL` & `SCOPE_INSTANCE` scenarios)

- `resourceInstancePK`: if the scope is for individual instance, then a value must be set here e.g. the primary key of a 'patient'

- `ownerPK` : the primary key of the owner of this object. This is necessary as after an object has been created, the owner must have access to this object.






## Testing
The unit tests are run using sqlite in memory database from the main folder.

1) Under `app/config/testing/database.php`, configure as follow:

```
   'default' => 'sqlite',
   
   'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		),
```

Remember to run migration and seeding (use PermitTestDatabaseSeeder) to setup test data.

2) Locate phpunit.xml under main app (not the package), add a test suite:

```
 <testsuites>
        <testsuite name="Application Test Suite">
            <directory>./app/tests/</directory>
        </testsuite>

	     <!-- Permit package -->
         <testsuite name="Permit">
            <directory>./workbench/nth/permit/tests/</directory>
        </testsuite>

    </testsuites>
```

3) Run unit tests:

```
$ phpunit --testsuite=Permit
```







