<?php

use Illuminate\Database\Seeder;

class RootFolderSeeder extends Seeder
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
    }
}
