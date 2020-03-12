<?php

use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('portal_users')
            ->insert(['id'=>1000,'first_name' => 'TestUser', 'last_name' => 'Global', 'email' => 'global_test@yourcompany.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'global', 'organization_id'=>1]);

        DB::table('portal_users')
            ->insert(['id'=>1001,'first_name' => 'TestUser', 'last_name' => 'MineOnlyParent1', 'email' => 'mine_only1_test@yourcompany.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'mine_only', 'organization_id'=>1]);

        DB::table('portal_users')
            ->insert(['id'=>1002,'first_name' => 'TestUser', 'last_name' => 'MineOnlyChild1', 'email' => 'mine_only2_test@yourcompany.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'mine_only', 'organization_id'=>1]);

        DB::table('portal_users')
            ->insert(['id'=>1003,'first_name' => 'TestUser', 'last_name' => 'MineOnlyChild2', 'email' => 'mine_only3_test@yourcompany.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'mine_only', 'organization_id'=>1]);

        DB::table('portal_users')
            ->insert(['id'=>1004,'first_name' => 'TestUser', 'last_name' => 'MineOnlyChild3', 'email' => 'mine_only4_test@yourcompany.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'mine_only', 'organization_id'=>1]);


        DB::table('portal_users')
            ->insert(['id'=>1005,'first_name' => 'TestUser', 'last_name' => 'MineOnlyChild4', 'email' => 'mine_only5_test@yourcompany.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'mine_only', 'organization_id'=>1]);

        DB::table('portal_users')
            ->insert(['id'=>1006,'first_name' => 'TestUser', 'last_name' => 'MineOnlyChild6', 'email' => 'mine_only6_test@yourcompany.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'mine_only', 'organization_id'=>1]);

        DB::table('portal_users')
            ->insert(['id'=>1007,'first_name' => 'TestUser', 'last_name' => 'OperatorTest', 'email' => 'operator_test@yourcompany.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'operator', 'organization_id'=>1]);

        //Establish Parent Child Relationships
        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>1006, 'parent_id'=>1005]);

        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>1002, 'parent_id'=>1001]);

        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>1003, 'parent_id'=>1001]);

        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>1004, 'parent_id'=>1001]);

        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>1004, 'parent_id'=>1001]);

        //Set Object Ownerships
        DB::table('portal_object_ownership')
            ->insert(['user_id'=>1006, 'organization_id'=>1, 'object_type'=>'menu', 'object_id' =>105767]);

        DB::table('portal_object_ownership')
            ->insert(['user_id'=>1002, 'organization_id'=>1, 'object_type'=>'menu', 'object_id' =>102777]);

        DB::table('portal_object_ownership')
            ->insert(['user_id'=>1004, 'organization_id'=>1, 'object_type'=>'menu', 'object_id' =>186]);

        DB::table('portal_object_ownership')
            ->insert(['user_id'=>1003, 'organization_id'=>1, 'object_type'=>'menu', 'object_id' =>196]);

        DB::table('portal_object_ownership')
            ->insert(['user_id'=>1001, 'organization_id'=>1, 'object_type'=>'menu', 'object_id' =>200]);

        

        //Create Operator Owner
        DB::table('portal_operator_merchant_map')
            ->insert(['user_id'=>1007, 'merchant_id'=>105381]);
        DB::table('portal_operator_merchant_map')
            ->insert(['user_id'=>1007, 'merchant_id'=>101651]);
    }
}
