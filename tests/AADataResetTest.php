<?php

use Tests\TestCase;
use App\Http\Controllers\MenuController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\Database;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\User;
use Illuminate\Support\Facades\Log;

class AADataResetTest extends TestCase {

    //use DatabaseTransactions;
    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();

        $db_username = env('SMAW_DB_USERNAME');
        $db_password = env('SMAW_DB_PASSWORD');
        $db_host = env('SMAW_DB_HOST');
        $db_port = env('SMAW_DB_PORT');
        $db_schema = env('SMAW_DB_DATABASE');

        $this->assertEquals(env('SMAW_DB_DATABASE'), 'smaw_unittest');

        //$handle = mysqli_connect(env('SMAW_DB_HOST'), env('SMAW_DB_USERNAME'), env('SMAW_DB_PASSWORD'));

        $response = shell_exec("mysql -h ".$db_host." --user=$db_username --password='$db_password' --port='$db_port' $db_schema < ./tests/sql/smaw_unittest_drop_all_tables.sql");

        $response = shell_exec("mysql -h ".$db_host." --user=$db_username --password='$db_password' --port='$db_port' $db_schema < ".env('SMAW_PATH')."/unit_tests/smaw_unittest_schema.sql");

        $response = shell_exec("mysql -h ".$db_host." --user=$db_username --password='$db_password' --port='$db_port' $db_schema < ./tests/sql/smaw_unittest_schema_complete.sql");

        $output = shell_exec('cd '.env('SMAW_PATH').' && ./vendor/bin/phinx migrate -e '.env('TESTING_ENV'));

        $response = shell_exec("mysql -h ".$db_host." --user=$db_username --password='$db_password' --port='$db_port' $db_schema < ./tests/sql/smaw_unittest_after_phinx_migrate_data_load.sql");


    }


    function testCreateDB() {
        $user_count = \App\User::count();
//        $this->assertTrue(true);
    }
}