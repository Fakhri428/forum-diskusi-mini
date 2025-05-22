<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    Category::insert([
        ['name' => 'Umum', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Laravel', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Frontend', 'created_at' => now(), 'updated_at' => now()],
    ]);
}

}
