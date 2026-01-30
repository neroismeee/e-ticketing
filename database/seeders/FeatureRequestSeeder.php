<?php

namespace Database\Seeders;

use App\Models\FeatureRequest;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class FeatureRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = FakerFactory::create('id_ID');
        for ($i=0; $i < 10; $i++) {
            FeatureRequest::create([
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'request_type' => $faker->randomElement(['feature_request', 'bug_fix']),
                'priority' => $faker->randomElement(['low', 'medium', 'high', 'critical']),
                'status' => $faker->randomElement([
                    'submission',
                    'pending_approval',
                    'approved',
                    'assigned',
                    'development',
                    'testing',
                    'validation',
                    'completed',
                    'post_implementation_review',
                    'rejected',
                    'cancelled',
                ]),
                'progress' => $faker->numberBetween(0, 100),
                'reporter_id' => $faker->numberBetween(1, 10),
                'assigned_to_id' => $faker->numberBetween(1, 10),
                'assigned_team' => $faker->randomElement(['programmer', 'network', 'hardware']),
                'date_submitted' => $faker->dateTimeBetween('-1 month', 'now'),
                'approval_date' => $faker->dateTimeBetween('-15 days', 'now'),
                'assignment_date' => $faker->dateTimeBetween('-10 days', 'now'),
                'start_date' => $faker->dateTimeBetween('-5 days', 'now'),
                'due_date' => $faker->dateTimeBetween('now', '+1 month'),
                'completion_date' => $faker->dateTimeBetween('now', '+2 months'),
                'review_date' => $faker->dateTimeBetween('now', '+3 months'),
                'estimated_effort' => $faker->numberBetween(1, 100),
                'actual_effort' => $faker->numberBetween(1, 100),
                'sla_time_elapsed' => $faker->numberBetween(1, 100),
                'sla_time_remaining' => $faker->numberBetween(1, 100),
                'sla_breached' => $faker->boolean,
                'approved_by' => $faker->numberBetween(1, 10),
                'rejection_reason' => $faker->optional()->sentence,
                'roi_impact' => $faker->optional()->paragraph,
                'quality_impact' => $faker->optional()->paragraph,
                'post_implementation_notes' => $faker->optional()->paragraph,
                'source_ticket_id' => $faker->numberBetween(1, 10),
                'is_direct_input' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
