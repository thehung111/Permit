<?php

namespace Nth\Permit\Constants;

class RoleConstants
{
	/**
	 * Role given for a super user who can manage all aspects of the website
	 */
	const SUPER_ADMIN = "Super Admin" ;
	
	/**
	 * Role should be given for a public user who is not logged in
	 */
	const GUEST = "Guest" ;	
	
	/**
	 * Role should be given for a logged in user
	 */
	const USER = "User" ;	
	
	/**
	 * Role given for the owner of a permission resource such as blog post, medical record ,etc
	 */
	const OWNER = "Owner" ;
	
}