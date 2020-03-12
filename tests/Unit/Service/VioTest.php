<?php

namespace Tests\Unit;


use App\User;
use App\Service\VioService;
use App\Http\Controllers\Merchant\PaymentController;

use Tests\TestCase;

class VioTest extends TestCase
{

    public function testSaveDailySummariesToday()
    {
        $paymentController = new PaymentController();
        $paymentController->saveDailySummariesToday();
    }

    public function testDwnloadVioDailySummaries()
    {
        $paymentController = new PaymentController();
        $paymentController->downloadVioDailySummaries();
    }
}