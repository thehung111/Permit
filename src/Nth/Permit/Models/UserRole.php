<?php

namespace Nth\Permit\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Nth\Permit\Helper\ConfigHelper;

class UserRole extends Eloquent{

	protected $table;

	public $timestamps = false;

	// disable primary key
	protected $primaryKey = null ;

	// disable auto-increment
	protected $incrementing = false;


	public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->table = ConfigHelper::getUserRoleTableName() ;
    }

   
    public function getUserIdAttribute($value)
	{
		return intval($value);
	}

	 public function getRoleIdAttribute($value)
	{
		return intval($value);
	}

}
