<?php

namespace App\Jobs;

use App\BusinessModel\Reports\ReportJob;
use App\BusinessModel\Reports\ScheduleFactory;
use App\Mail\ReportMail;
use App\Model\ReportSchedules;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class DailyTaskForReports implements ShouldQueue
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
     * @param ReportSchedules $model
     * @param ReportJob $reportJob
     * @param
     * @return void
     */
    public function handle(ReportSchedules $model, ReportJob $reportJob)
    {
        try {

            date_default_timezone_set('America/Denver');
            \DB::enableQueryLog();
            $records = $model
                ->where('available_at', '<=', Carbon::now()->timestamp)
                ->where('start_date', '<=', Carbon::now()->toDateString())
                ->where('end_date', '>=', Carbon::now()->toDateString())
                ->where('logical_delete', '=', 'N')
                ->get();

            if (!empty($records)) {
                /** @var ReportSchedules $record */
                foreach ($records as $record) {
                    try {
                        $payload = new Collection(json_decode($record->payload, true));
                        $reportJob->reportData = $payload;
                        $report = $reportJob->getReportCVS();
                        Mail::to($record['recipient'])->queue(new ReportMail($record['report_name'], $record['frequency'], 'Attached is your report , scheduled to be generated between ' . $record['start_date'] . ' and ' . $record['end_date'], $report));
                    } catch (\Exception $exception) {
                        \Log::error($exception->getMessage());
                    }
                    try {
                        $start_date = Carbon::parse($record['start_date'])->addHour(4)->timestamp;
                        $end_date = Carbon::parse($record['end_date'])->addHour(4)->timestamp;
                        $available_at = ScheduleFactory::getScheduleNextDateByFrequency($record['frequency']);
                        if ($available_at >= $start_date && $available_at <= $end_date) {
                            \Log::debug('Available At updated:');
                            \Log::debug($available_at);
                            $model->where('id', $record['id'])->update(['available_at' => $available_at]);
                        } else {
                            \Log::debug('Disable Schedule');
                            $model->where('id', $record['id'])->update(['logical_delete' => 'Y']);
                        }
                    } catch (\Exception $exception) {
                        \Log::error($exception->getMessage());
                        $model->where('id', $record['id'])->update(['logical_delete' => 'Y']);
                    }
                }
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
        }
    }
}