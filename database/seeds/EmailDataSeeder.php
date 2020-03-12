<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmailDataSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('Property')
            ->insert(['name' => 'onboarding_recipient_email', 'value' => 'support@yourcompany.com',
                'comments' => 'Order140 recipient email, who will receive the email notifications'
                    . ' when merchant form #2 has being completed by any customer.',
                'created' => Carbon::now()]);
        DB::table('portal_email_types')
            ->insert(['type' => 'generic_email',
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_email_types')
            ->insert(['type' => 'reseller_form_1',
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_email_types')
            ->insert(['type' => 'reseller_form_1_reminder',
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_email_types')
            ->insert(['type' => 'merchant_form_1',
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_email_types')
            ->insert(['type' => 'merchant_form_1_reminder',
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('portal_email_types')
            ->insert(['type' => 'merchant_form_2',
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
