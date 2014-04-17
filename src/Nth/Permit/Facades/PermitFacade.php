<?php

namespace Nth\Permit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Permit package facade
 */ 
class PermitFacade extends Facade {

    protected static function getFacadeAccessor() { return 'permit'; }

}