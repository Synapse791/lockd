<?php

use Illuminate\Database\Seeder;

class AdministratorsGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('au_group')->insert([
            'name' => 'Administrators',
        ]);
    }
}
