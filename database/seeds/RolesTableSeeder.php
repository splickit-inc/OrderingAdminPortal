<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('portal_roles')->insert([
            'name' => 'Order 140 Super User',
            'description'=> 'This is the Order 140 Super User, which has access to everything in the system.'
        ]);

        DB::table('portal_roles')->insert([
            'name' => 'Partner Admin',
            'description'=> 'This is a partner administrator.  Only the Order 140 Super User will have more functionality.'
        ]);

        DB::table('portal_roles')->insert([
            'name' => 'VAR Admin',
            'description'=> 'This is a value added reseller administrator.  They can manage all of the Brands/Merchants/Menus under their VAR organization.'
        ]);

        DB::table('portal_roles')->insert([
            'name' => 'Account Manager',
            'description'=> 'These users can only manage merchants that they have had assigned to them under their VAR Admin.'
        ]);

        DB::table('portal_roles')->insert([
            'name' => 'Customer Service',
            'description'=> 'Order 140 Customer Service Users'
        ]);

        DB::table('portal_roles')->insert([
            'name' => 'Store Owner Operator',
            'description'=> 'Access to only their own merchant'
        ]);

        DB::table('portal_roles')->insert([
            'name' => 'Store Manager',
            'description'=> 'Only access to the store they manage.'
        ]);
    }
}
