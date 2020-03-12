<?php

namespace Tests\Unit\CustomerServiceSearch;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\CustomerServiceSearch\UserSearch;
use App\User as PortalUser;

class OrderSearchTest extends TestCase
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
        $response = $this->call('POST', '/orders_search', ['search_text' => 'Rosenthal']);
        $response = json_decode($response->content());

        $user_ids = array_column($response->data, 'user_id');

        $this->assertTrue(in_array(20000, $user_ids));
    }
}