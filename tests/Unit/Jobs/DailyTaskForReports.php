<?php


namespace Tests\Unit\Jobs;


use App\BusinessModel\Reports\ReportJob;
use App\Model\ReportSchedules;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DailyTaskForReports extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function AssetDailyReports()
    {
        \DB::beginTransaction();
        /** @var \App\Jobs\DailyTaskForReports $job */
        $job = \App::make(\App\Jobs\DailyTaskForReports::class);
        $job->handle(\App::make(ReportSchedules::class), \App::make(ReportJob::class));
        \DB::rollBack();
    }

    /** @test */
    public function testDailyGetTransactionsCSV()
    {
        $reportSchedule = ReportSchedules::find(41);

        $rptServ = new \App\Service\ReportExportationService();

        $reportJob = new  \App\BusinessModel\Reports\ReportJob($rptServ);
        $reportJob->reportData = json_decode($reportSchedule->payload, true);

        $reportJob->getReportCVS();
    }

    /** @test */
    public function testDailyGetUsersCSV()
    {
        $reportSchedule = ReportSchedules::find(44);

        $rptServ = new \App\Service\ReportExportationService();

        $reportJob = new  \App\BusinessModel\Reports\ReportJob($rptServ);
        $reportJob->reportData = json_decode($reportSchedule->payload, true);

        $reportJob->getReportCVS();
    }

    /** @test */
    public function testDailyGetMenuItemsCSV()
    {
        $reportSchedule = ReportSchedules::find(46);

        $rptServ = new \App\Service\ReportExportationService();

        $reportJob = new  \App\BusinessModel\Reports\ReportJob($rptServ);
        $reportJob->reportData = json_decode($reportSchedule->payload, true);

        $reportJob->getReportCVS();
    }

    /** @test */
    public function testDailyReportJob()
    {
        $dtR = new \App\Jobs\DailyTaskForReports();

        $reportSchedule = new \App\Model\ReportSchedules();
        $reportJob = new \App\BusinessModel\Reports\ReportJob(new \App\Service\ReportExportationService());

        $dtR->handle($reportSchedule, $reportJob);
    }

    /** @test */
    public function testDailyReportJobUser()
    {
        $dtR = new \App\Jobs\DailyTaskForReports();

        $reportSchedule = new \App\Model\ReportSchedules();
        $reportJob = new \App\BusinessModel\Reports\ReportJob(new \App\Service\ReportExportationService());

        $dtR->handle($reportSchedule, $reportJob);
    }

}