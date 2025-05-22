<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    Tag::insert([
        ['name' => 'php', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'bootstrap', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'vuejs', 'created_at' => now(), 'updated_at' => now()],
    ]);
}

}
