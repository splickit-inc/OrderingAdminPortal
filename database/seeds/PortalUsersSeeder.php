<?php

use Illuminate\Database\Seeder;

class PortalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('portal_users')->insert(['first_name' => 'Brian', 'last_name' => 'ONeill', 'email' => 'briantamu6@gmail.com', 'password' => '$2y$10$1JDPFVM.1/pbdBfJKCP8bOffA23rnK9CLbxDHJ0B3rI6nFW6/Lwpa', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Brian', 'last_name' => 'ONeill', 'email' => 'boneill@yourcompany.com', 'password' => '$2y$10$vWu9C3O9Ck2Wn92W18gy3e2TrW0ingW200tYpl.ohcEdlxI/x1rlS', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Joe', 'last_name' => 'Doe', 'email' => 'fake@yourcompany.com', 'password' => '$2y$10$1AdZPA85/OIsG9V5n52O8us8HVi1JiEc3ablaeakrfGUw9.IMgbAG', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'bob', 'last_name' => 'mich', 'email' => 'bob@yourcompany.com', 'password' => '$2y$10$FgxpEWF1dGTqnTG2Zoyilu.8Lu3rQKKA3TE3KyfRP9DwChvOnXZNm', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Bob', 'last_name' => 'Test', 'email' => 'bobby@yourcompany.com', 'password' => '$2y$10$kFxzpkHcbWxAnvZUmX.V8OTUT9Wm5cKlF3UB56iULN9/uKLQNC5r2', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Michael', 'last_name' => 'Jord', 'email' => 'Jordan@yourcompany.com', 'password' => '$2y$10$Pk.zpjE0MetEFlm.7zz7gO7zB6wNBVAzA87LEbPd4fY7kB.CoH4Ta', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Brian', 'last_name' => 'Urlacher', 'email' => 'BUrlacher@yourcompany.com', 'password' => '$2y$10$SMbfFcbFcRfs7tO0QmHrReZ4wpyhk6UUx.HB2lXxha8brCXiO4blC', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Bobby', 'last_name' => 'Brown', 'email' => 'bbrown@splickit', 'password' => '$2y$10$e9iEuDwAdZv6c4582l/c5untrZW1QIWb3xCIcQrtClPvyDnHbGQ52', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Oscar', 'last_name' => 'Jara', 'email' => 'oajara@gmail.com', 'password' => '$2y$10$7SIOr.VtN8TksvrA8Sl3fuIs7tx3UaGxPzXW2yRllid1THXCygRn2', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Layne', 'last_name' => 'Sheppard', 'email' => 'lsheppard@yourcompany.com', 'password' => '$2y$10$GYgpkv7Cwqk9Kk2B1S7Rou77ODr8gHJxrvCvbbhEW1WdBYYY6WhhK', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Tarek', 'last_name' => 'Dimachkie', 'email' => 'tdimachkie@yourcompany.com', 'password' => '$2y$10$AqMpKXHjRc23.wpOEyVOT.9cSqdJagcp5bVhdX09ou0hDu8miarcu', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Cary', 'last_name' => 'Russell', 'email' => 'crussell@yourcompany.com', 'password' => '$2y$10$RBryW1io2bJ3enaBxl4JTOU7sLcpENwxnRzzU8akVWJUHhnnVilXu', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Adam', 'last_name' => 'Rosenthal', 'email' => 'arosenthal@yourcompany.com', 'password' => '$2y$10$2itHemr7mKLnVjdgdthML.9iTq0nUrFCbSO2bpaU4BSH8wq2fpHnC', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Ryan', 'last_name' => 'Ballein', 'email' => 'rballein@yourcompany.com', 'password' => '$2y$10$/jsqkCYmOuFaqqObnqy1tOBEWe5b.evu5NpfthbC1TKrmEeiA6FUS', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Kendall', 'last_name' => 'Russell', 'email' => 'krussell@yourcompany.com', 'password' => '$2y$10$wyIVoLlyFHP4uM4wahP0EO62JfTAyiCr3Cp.qA7ItvD.ixuykd9D.', 'visibility'=>'global', 'organization_id'=>1]);
        DB::table('portal_users')->insert(['first_name' => 'Mikiko', 'last_name' => 'Long', 'email' => 'mlong@yourcompany.com', 'password' => '$2y$10$1YiNQkC5pMJme1Ves9HXA.geN023pAEW6B8jdb8OzKQR3FUxlqzSa', 'visibility'=>'global', 'organization_id'=>1]);
    }
}
