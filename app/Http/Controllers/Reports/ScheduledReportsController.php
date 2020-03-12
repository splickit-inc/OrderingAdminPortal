<?php


namespace App\Http\Controllers\Reports;
use App\Model\ReportSchedules;
use App\Http\Controllers\Controller;
use Request;
use \Illuminate\Support\Facades\Auth;

class ScheduledReportsController extends Controller
{
    public function index() {
        $reports = \DB::table('portal_report_schedules')
            ->join('portal_object_ownership', 'portal_report_schedules.id', 'portal_object_ownership.object_id')
            ->where('portal_object_ownership.object_type', '=', 'schdl_rpt')
            ->where('portal_object_ownership.user_id', '=', Auth::user()->id)
            ->get()
            ->toArray();

        return $reports;
    }

    public function update() {
        $data = Request::all();

        $report_schedule = ReportSchedules::find($data['id']);

        $report_schedule->report_name = $data['report_name'];
        $report_schedule->frequency = $data['frequency'];
        $report_schedule->recipient = $data['recipient'];

        $report_schedule->save();

        return $report_schedule;
    }

    public function destroy($scheduled_report) {
        $report_schedule = ReportSchedules::destroy($scheduled_report);
        return $report_schedule;
    }
}