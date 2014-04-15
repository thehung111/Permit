<?php
use Mockery as m;
use \Illuminate\Support\Facades\Config;

class BaseTestCase extends \Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		// include the bootstrap file from parent folder
		return require __DIR__.'/../../../../../bootstrap/start.php';
	}


	public function assertArraySame($expectedResultsArr, $arr)
	{
		$this->assertEmpty(array_merge(array_diff($arr, $expectedResultsArr), array_diff($expectedResultsArr, $arr)));
	}

	public function extractKeyArr($arr, $keyToExtract)
	{
		$result = array();
		foreach($arr  as $item)
		{
			$result[] = $item[$keyToExtract];
		}
		return $result;
	}

	// reference: https://github.com/laravel/framework/issues/1181
	private function resetEvents()
	{
	    // Define the models that have event listeners.
	    $models = array('\Nth\Permit\Models\Role', '\Nth\Permit\Models\ResourceAction');

	    // Reset their event listeners.
	    foreach ($models as $model) {

	        // Flush any existing listeners.
	        call_user_func(array($model, 'flushEventListeners'));

	        // Reregister them.
	        call_user_func(array($model, 'boot'));
	    }
	}

	public function setUp()
	{
		parent::setUp();

		$this->resetEvents();
		$this->prepare_data_for_tests();

	}

	public function prepare_data_for_tests()
	{
		// Note: reset can only be called if test database is mysql
		// currently use sqlite for testing this is unnecessary
		if(Config::get('database.default') != 'sqlite')
			Artisan::call('migrate:reset');

		Artisan::call('migrate', array("--bench" => "nth/permit"));

		Artisan::call('db:seed', array("--class" => "\\Nth\\Permit\\Seeds\\PermitTestDatabaseSeeder"));

	}

	public function tearDown()
	{
	    parent::tearDown();

	    m::close();
	}


}
