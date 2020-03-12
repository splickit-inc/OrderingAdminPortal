<?php

use Tests\TestCase;
use App\Http\Controllers\MenuController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\Database;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\User;
use App\Model\Merchant;
use App\Model\ObjectOwnership;
use Illuminate\Support\Facades\Log;

class MerchantControllerTest extends TestCase {

    //use DatabaseTransactions;
    use WithoutMiddleware;
    use DatabaseTransactions;

    //protected $baseUrl = env('TESTING_URL');

    public function loginAsSuperUser() {
        $response = $this->call('POST', '/login_attempt', ['email'=>'SuperUser.Tester@yourcompany.com', 'password'=>'Test']);
    }

    public function testCreateMerchantSuperUser()
    {
        $this->loginAsSuperUser();

        $merchant = factory(App\Model\Merchant::class)->make();

        $data = $merchant->toArray();
        $data['brand'] = $data['brand_id'];
        $data['time_zone'] = [];
        $data['time_zone']['type_id_value'] = -6;

        $response = $this->call('POST', '/create_merchant', $data);

        $db_merchant = Merchant::where('shop_email', '=', $merchant->shop_email)->first();

        $this->assertEquals($db_merchant->name, $merchant->name);
        $this->assertEquals($db_merchant->brand_id, $merchant->brand_id);

        $ownership_count = ObjectOwnership::where('user_id', '=', 1)->where('object_type', '=', 'merchant')->where('object_id', '=', $db_merchant->merchant_id)->count();

        $this->assertGreaterThan(0, $ownership_count);
    }

    public function testMerchantSearch() {
        $response = $this->call('POST', '/merchant_search', ['search_text'=>'', 'password'=>'Test']);
    }
}