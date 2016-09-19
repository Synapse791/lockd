<?php

use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('da_folder')->insert([
            'parent_id' => 1,
            'name' => 'Folder 1.1',
        ]);

        DB::table('da_folder')->insert([
            'parent_id' => 1,
            'name' => 'Folder 1.2',
        ]);

        DB::table('da_folder')->insert([
            'parent_id' => 2,
            'name' => 'Folder 2.1',
        ]);

        DB::table('da_folder')->insert([
            'parent_id' => 3,
            'name' => 'Folder 3.1',
        ]);

        factory(\Lockd\Models\Password::class)->create(['folder_id' => 1]);
        factory(\Lockd\Models\Password::class)->create(['folder_id' => 1]);
        factory(\Lockd\Models\Password::class)->create(['folder_id' => 5]);
        factory(\Lockd\Models\Password::class)->create(['folder_id' => 5]);
        factory(\Lockd\Models\Password::class)->create(['folder_id' => 4]);
        factory(\Lockd\Models\Password::class)->create(['folder_id' => 4]);

        DB::table('au_group_folders')->insert([
            'group_id' => 1,
            'folder_id' => 2,
        ]);

        DB::table('au_group_folders')->insert([
            'group_id' => 1,
            'folder_id' => 3,
        ]);

        DB::table('au_group_folders')->insert([
            'group_id' => 1,
            'folder_id' => 4,
        ]);

        DB::table('au_group_folders')->insert([
            'group_id' => 1,
            'folder_id' => 5,
        ]);
    }
}
