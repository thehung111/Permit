<?php

namespace Nth\Permit\Facade;

use Illuminate\Support\Facades\Facade;

class PermitFacade extends Facade {

    protected static function getFacadeAccessor() { return 'permit'; }

}