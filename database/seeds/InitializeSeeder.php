<?php

use Illuminate\Database\Seeder;

class InitializeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('da_folder')->insert([
            'parent_id' => 0,
            'name' => 'Root',
        ]);

        DB::table('au_group')->insert([
            'name' => 'Administrators',
        ]);
    }
}
