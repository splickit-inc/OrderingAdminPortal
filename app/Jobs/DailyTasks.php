<?php

namespace App\Jobs;

use App\Service\LeadRemainderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\Merchant\PaymentController;

class DailyTasks implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     * @param LeadRemainderService $remainderService
     * @return void
     */
    public function handle(LeadRemainderService $remainderService)
    {
        // Here goes daily tasks that we want to get executed every day
        $remainderService->reviewSignUpReminders();
        $remainderService->reviewWelcomeReminders();

        $paymentController = new PaymentController();
        $paymentController->saveDailySummariesToday();
    }
}
