<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run()
    {
        // Create default statuses
        $statuses = [
            ['name' => 'New', 'is_default' => true],
            ['name' => 'In Progress', 'is_default' => false],
            ['name' => 'Closed', 'is_default' => false],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}
