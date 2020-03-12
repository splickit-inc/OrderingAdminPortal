<?php

namespace Tests\Unit\CustomerServiceSearch;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\CustomerServiceSearch\UserSearch;
use App\User as PortalUser;

class UserSearchTest extends TestCase
{
    use WithoutMiddleware;
    public $baseUrl;

    public function __construct() {
        $this->baseUrl = env('PORTAL_BASE_URL');
    }

    public function testGlobalSearch() {
        $user = PortalUser::find(2);

        $this->actingAs($user)
            ->withSession(['user_visibility' => 'global']);

        //Search On Last Name
        $response = $this->call('GET', '/user_search', ['search_text' => 'Rosenthal']);
        $response = json_decode($response->content());

        $user_ids = array_column($response->data, 'user_id');

        $this->assertTrue(in_array(20000, $user_ids));

        //Search On User Id
        $response = $this->call('GET', '/user_search', ['search_text' => '20154']);
        $response = json_decode($response->content());

        $user_ids = array_column($response->data, 'user_id');

        $this->assertTrue(in_array(20154, $user_ids));

        //Search On User Email
        $response = $this->call('GET', '/user_search', ['search_text' => 'ewfedosky@gmail.com']);
        $response = json_decode($response->content());

        $user_ids = array_column($response->data, 'user_id');

        $this->assertTrue(in_array(20181, $user_ids));

        //Search On User Email
        $response = $this->call('GET', '/user_search', ['search_text' => 'Rosalie']);
        $response = json_decode($response->content());

        $user_ids = array_column($response->data, 'user_id');

        $this->assertTrue(in_array(20189, $user_ids));

        //Search On User Contact No
        $response = $this->call('GET', '/user_search', ['search_text' => '(303)818-9976']);
        $response = json_decode($response->content());

        $user_ids = array_column($response->data, 'user_id');

        $this->assertTrue(in_array(20642, $user_ids));
    }

    public function testBrandSearch() {
        $user = PortalUser::find(5050);

        $this->actingAs($user)
            ->withSession(['user_visibility' => 'brand', 'brand_manager_brand'=>150]);

        //Search On Last Name
        $response = $this->call('GET', '/user_search', ['search_text' => 'Rosenthal']);
        $response = json_decode($response->content());

        $user_ids = array_column($response->data, 'user_id');

        $this->assertFalse(in_array(20000, $user_ids));

        //Search On Last Name
        $response = $this->call('GET', '/user_search', ['search_text' => 'Uchimoto']);
        $response = json_decode($response->content());

        $user_ids = array_column($response->data, 'user_id');

        $this->assertTrue(in_array(20016, $user_ids));
    }
}