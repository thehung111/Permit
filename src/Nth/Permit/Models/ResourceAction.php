<?php

namespace Nth\Permit\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Nth\Permit\Helper\ConfigHelper;

class ResourceAction extends Eloquent{

	protected $table;

	public $timestamps = false;
	protected $primaryKey = "resourceActionId" ;

	public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->table = ConfigHelper::getResourceActionTableName() ;
    }

    // sqlite will complain if dun do this
    public function getBitwiseValueAttribute($value)
	{
		return intval($value);
	}

}
