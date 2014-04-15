<?php

namespace Nth\Permit\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Nth\Permit\Helper\ConfigHelper;

class ResourcePerm extends Eloquent{

	protected $table;

	public $timestamps = false;
	protected $primaryKey = "resourcePermId" ;


	public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->table = ConfigHelper::getResourcePermTableName() ;
    }

   
    public function getActionsBitwiseValueAttribute($value)
	{
		return intval($value);
	}

}
