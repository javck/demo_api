<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();


        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); //關閉外鍵偵測

        //順序有講究，請特別注意!!
        $this->call(CategorySeeder::class);
        $this->call(TagSeeder::class);
        $this->call(PostSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); //重開外鍵偵測
    }
}
