<?php

namespace Nth\Permit\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Nth\Permit\Helper\ConfigHelper;


class Role extends Eloquent{

	protected $table;

	public $timestamps = false;
	protected $primaryKey = "roleId" ;


	public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->table = ConfigHelper::getRoleTableName() ;
    }

    public static function boot()
    {
        parent::boot();
        

        // Setup event bindings
        Role::saving(function($role) 
		{
			//\Illuminate\Support\Facades\Log::info('saving role');

			// set timestamp
			if(!$role->exists)
			{
				$role->createDate = new \DateTime;
			}

			$role->modifiedDate = new \DateTime;


		    return true;
		});
    }

    /**
	 * Get the attributes that should be converted to dates.
	 *
	 * @return array
	 */
	public function getDates()
	{
		return array();
	}

	public function getRoleIdAttribute($value)
	{
		return intval($value);
	}

}
