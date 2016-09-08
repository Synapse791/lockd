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

        DB::table('au_group_folders')->insert([
            'group_id' => 1,
            'folder_id' => 1,
        ]);
    }
}
