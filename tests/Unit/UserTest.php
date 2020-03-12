<?php

namespace Tests\Unit;


use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function getGlobalUser()
    {
        \DB::beginTransaction();
        $user = factory(User::class)->create();
        $this->checkValidUser($user);
        $this->actingAs($user);
        \DB::rollBack();
    }

    /**
     * @param User $user
     */
    protected function checkValidUser($user)
    {
        $this->assertNotNull($user->organization(), 'The user model does not have organization relation');
        $user_organization = $user->organization()->first();
        $this->assertNotNull($user_organization, 'The user does not belong to an organization');
        $this->assertNotNull($user->visibility, 'The user does not have visibility');
    }
}