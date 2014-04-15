<?php

namespace Nth\Permit\Constants;

/**
 * Constants for resource permissions and scoping
 */ 
class ResourceConstants
{
	/**
	 * permission scope apply to all instances of this resource e.g. edit all blog posts
	 */
	const SCOPE_ALL = 0;	


	/**
	 * permission scope of a resource instance or an individual resource 
	 * e.g. edit a particular blog post with id 1234
	 */
	const SCOPE_INSTANCE = 1;	




}