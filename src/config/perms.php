<?php

// array of resource name => supported actions (name and bitwise value)
return array(
	/**
	 * example:
	 * The resource name here is "user". 
	 * The possible actions that can be applied to this resource are user.view, user.add, user.edit, user.delete
	 * the bitwiseValue must be a power of 2. Max value is 2^30
	 * Each resource can have a max of 30 permissions 
	 * (1 int only have 32 bits, 1 for sign bit, 1 will be reserved for our own purpose) 
	 */
	'initial_perm_resources' => array( 
		'user' => array(
			array('actionName' => 'user.add'		, 'bitwiseValue' => 1),
			array('actionName' => 'user.edit'		, 'bitwiseValue' => 2),
			array('actionName' => 'user.delete'		, 'bitwiseValue' => 4),
			array('actionName' => 'user.view'		, 'bitwiseValue' => 8),
		),

		/*
		// another example for "blog_post" resource
		'blog_post' => array(
			array('actionName' => 'post.add'		, 'bitwiseValue' => 1),
			array('actionName' => 'post.edit'		, 'bitwiseValue' => 2),
			array('actionName' => 'post.delete'	, 'bitwiseValue' => 4),
			array('actionName' => 'post.view'		, 'bitwiseValue' => 8),
		),
		*/
	),

);