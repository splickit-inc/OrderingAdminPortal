<?php

use Illuminate\Database\Seeder;
use \App\Model\EmailType;
use Carbon\Carbon;

class ReminderPeriodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rf1ReminderType = EmailType::resellerForm1ReminderType();
        DB::table('portal_reminder_periods')
            ->insert(['time_lapse'=>3, 'unit'=>'days', 'email_type_id' =>$rf1ReminderType['id'],
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_reminder_periods')
            ->insert(['time_lapse'=>7, 'unit'=>'days', 'email_type_id' =>$rf1ReminderType['id'],
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_reminder_periods')
            ->insert(['time_lapse'=>14, 'unit'=>'days', 'email_type_id' =>$rf1ReminderType['id'],
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        $mf1ReminderType = EmailType::merchantForm1ReminderType();
        DB::table('portal_reminder_periods')
            ->insert(['time_lapse'=>3, 'unit'=>'days', 'email_type_id' =>$mf1ReminderType['id'],
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_reminder_periods')
            ->insert(['time_lapse'=>5, 'unit'=>'days', 'email_type_id' =>$mf1ReminderType['id'],
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_reminder_periods')
            ->insert(['time_lapse'=>7, 'unit'=>'days', 'email_type_id' =>$mf1ReminderType['id'],
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
