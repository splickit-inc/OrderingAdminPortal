<?php

use Tests\TestCase;
use App\Http\Controllers\MenuController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\Database;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\User;
use App\Model\Merchant;
use App\Model\MenuItem;
use App\Model\Promo;
use App\Model\ObjectOwnership;
use Illuminate\Support\Facades\Log;

class ABEndToEndTestingTest extends TestCase {

    protected $created_merchant_id;

    public function testAssertMerchantDataCorrect() {
        $merchant = Merchant::where('name', '=', 'Test Merchant e2e')->first();
        $this->created_merchant_id = $merchant->merchant_id;


        $this->seeInDatabase('Merchant', ['name'=>'Test Merchant e2e']);
    }

    public function testAssertMerchantContactCreated() {
        $merchant = Merchant::where('name', '=', 'Test Merchant e2e')->first();
        $this->created_merchant_id = $merchant->merchant_id;

        $this->seeInDatabase('adm_merchant_email', ['email'=>'AdminContactEmail@yourcompany.com', 'merchant_id'=> $this->created_merchant_id]);
        $this->seeInDatabase('adm_merchant_phone', ['phone_no'=>'5555555555', 'merchant_id'=> $this->created_merchant_id]);
    }

    public function testAssertMerchantDeliveryCreated() {
        $merchant = Merchant::where('name', '=', 'Test Merchant e2e')->first();
        $this->created_merchant_id = $merchant->merchant_id;

        $this->seeInDatabase('Merchant_Delivery_Info', ['minimum_delivery_time'=>'10', 'merchant_id'=> $this->created_merchant_id]);
        $this->seeInDatabase('Merchant_Delivery_Price_Distance', ['minimum_order_amount'=>'15', 'merchant_id'=> $this->created_merchant_id]);
    }

    public function testAssertMerchantGeneralInfoCreated() {
        $merchant = Merchant::where('name', '=', 'Test Merchant e2e')->first();
        $this->created_merchant_id = $merchant->merchant_id;

        $this->seeInDatabase('Merchant', ['display_name'=>'Test Display', 'phone_no'=>'1234567890','merchant_id'=> $this->created_merchant_id]);
        $this->seeInDatabase('Merchant', ['lead_time'=>'11', 'merchant_id'=> $this->created_merchant_id]);
        $this->seeInDatabase('Merchant', ['custom_order_message'=>'Test Order Message', 'merchant_id'=> $this->created_merchant_id]);
    }

    public function testAssertMenuCreated() {
        $this->seeInDatabase('Menu', ['name'=>'Test Menu']);
    }

    public function testAssertMenuTypeCreated() {
        $this->seeInDatabase('Menu_Type', ['menu_type_name'=>'Burgers']);
    }

    public function testAssertModifierGroupCreated() {
        $this->seeInDatabase('Modifier_Group', ['modifier_group_name'=>'Burger Toppings']);
    }

    public function testAssertMenuEditItem() {
        $this->seeInDatabase('Item', ['item_name'=>'Mushroom Swiss2']);
    }

    public function testAssertModifierItemUpdate() {
        $this->seeInDatabase('Modifier_Item', ['modifier_item_name'=>'Shredded Lettuce']);
    }

    public function testAssertPromoType1Created() {
        $this->seeInDatabase('Promo', ['promo_type'=>'1', 'description'=>'E2E Promo Type 1']);

        $promo_type_1 = Promo::where('promo_type', '=', 1)->where('description', '=','E2E Promo Type 1')->first();

        $this->seeInDatabase('Promo_Key_Word_Map', ['promo_id'=> $promo_type_1->promo_id,
            'promo_key_word'=>'PromoType1Key1']);

        $this->seeInDatabase('Promo_Key_Word_Map', ['promo_id'=> $promo_type_1->promo_id,
            'promo_key_word'=>'PromoType1Key2']);

        $this->seeInDatabase('Promo_Type1_Amt_Map', ['promo_id'=> $promo_type_1->promo_id,
            'max_amt_off'=>7.00]);

    }

    //Manage Users Tests
    public function testAssertUsersCreated() {
        $this->seeInDatabase('portal_users', ['email'=>'Joe.Tester@yourcompany.com']);

        $this->seeInDatabase('portal_users', ['email'=>'Test.Operator@yourcompany.com']);

        $operator_user = User::where('email', '=', 'Test.Operator@yourcompany.com')->first();

        $this->seeInDatabase('portal_operator_merchant_map', ['user_id'=>$operator_user->id]);
    }

}