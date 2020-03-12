<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LookupTest extends TestCase
{
    use DatabaseTransactions;

    public function testIssueCreation()
    {
        factory(App\Issues::class)->create([
            'subject' => 'NAIL POPS!'
        ]);

        $this->seeInDatabase('issues', ['subject' => 'NAIL POPS!']);
    }
}