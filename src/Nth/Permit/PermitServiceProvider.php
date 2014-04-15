<?php namespace Nth\Permit;

use Illuminate\Support\ServiceProvider;
use Nth\Permit\Helper\ConfigHelper;

class PermitServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('nth/permit');

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->registerRoleService();
		$this->registerResourceActionDataProvider();
		$this->registerPermService();
		$this->registerPermitService();
	}

	/**
	 * Register role service: permit.RoleService
	 * 
	 * @return void
	 */
	public function registerRoleService()
	{
		$this->app->singleton('permit.RoleService' , function()
	    {
	        return new \Nth\Permit\Services\RoleService;
	    });
	}

	/**
	 * Register the resource action data provider: permit.ResourceActionService
	 */
	public function registerResourceActionDataProvider()
	{
		$this->app->singleton('permit.ResourceActionService' , function()
	    {
	        $data_provider = ConfigHelper::getDataProvider();

	        switch ($data_provider)
			{
				case 'config_file':
					return new \Nth\Permit\Services\ConfigFileResourceActionService;
					break;

				case 'database':
					return new \Nth\Permit\Services\DatabaseResourceActionService;
					break;
			}

			throw new \InvalidArgumentException("Invalid data provider: [$data_provider] chosen for Permit.");

	    });
	}


	/**
	 * Register permission service: permit.ResourcePermService
	 * 
	 * @return void
	 */
	public function registerPermService(){
		$this->app->singleton('permit.ResourcePermService' , function($app)
	    {
	        return new \Nth\Permit\Services\ResourcePermService($app['permit.RoleService'] , $app['permit.ResourceActionService']);
	    });
	}


	/**
	 * Register the permit service: permit
	 */
	public function registerPermitService()
	{
		$this->app->singleton('permit' , function($app)
	    {
	        return new Permit($app['permit.RoleService'] , $app['permit.ResourceActionService'], $app['permit.ResourcePermService']);
	    });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('permit.ResourceActionService', 'permit.RoleService', 'permit.ResourcePermService', 'permit');
	}

}
