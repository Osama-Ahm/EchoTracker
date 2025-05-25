<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Forum;
use App\Models\Badge;
use Illuminate\Support\Str;

class CommunityEngagementSeeder extends Seeder
{
    public function run(): void
    {
        // Create default forums
        $forums = [
            [
                'name' => 'General Discussion',
                'description' => 'General environmental topics and community discussions',
                'slug' => 'general-discussion',
                'color' => '#2d5a27',
                'icon' => 'bi-chat-dots',
                'sort_order' => 1,
            ],
            [
                'name' => 'Pollution Reports',
                'description' => 'Discuss pollution incidents and solutions',
                'slug' => 'pollution-reports',
                'color' => '#dc3545',
                'icon' => 'bi-exclamation-triangle',
                'sort_order' => 2,
            ],
            [
                'name' => 'Wildlife Conservation',
                'description' => 'Wildlife protection and conservation efforts',
                'slug' => 'wildlife-conservation',
                'color' => '#28a745',
                'icon' => 'bi-tree',
                'sort_order' => 3,
            ],
            [
                'name' => 'Sustainable Living',
                'description' => 'Tips and discussions about sustainable lifestyle choices',
                'slug' => 'sustainable-living',
                'color' => '#17a2b8',
                'icon' => 'bi-recycle',
                'sort_order' => 4,
            ],
            [
                'name' => 'Climate Action',
                'description' => 'Climate change discussions and action plans',
                'slug' => 'climate-action',
                'color' => '#ffc107',
                'icon' => 'bi-thermometer-sun',
                'sort_order' => 5,
            ],
            [
                'name' => 'Community Events',
                'description' => 'Organize and discuss community environmental events',
                'slug' => 'community-events',
                'color' => '#6f42c1',
                'icon' => 'bi-calendar-event',
                'sort_order' => 6,
            ],
            [
                'name' => 'Solutions & Innovation',
                'description' => 'Share innovative environmental solutions and technologies',
                'slug' => 'solutions-innovation',
                'color' => '#fd7e14',
                'icon' => 'bi-lightbulb',
                'sort_order' => 7,
            ],
            [
                'name' => 'Local Initiatives',
                'description' => 'Local environmental projects and initiatives',
                'slug' => 'local-initiatives',
                'color' => '#20c997',
                'icon' => 'bi-geo-alt',
                'sort_order' => 8,
            ],
        ];

        foreach ($forums as $forumData) {
            Forum::firstOrCreate(
                ['slug' => $forumData['slug']],
                $forumData
            );
        }

        // Create default badges
        $badges = [
            [
                'name' => 'First Reporter',
                'description' => 'Reported your first environmental incident',
                'icon' => 'bi-award',
                'color' => '#28a745',
                'criteria' => json_encode(['incidents_reported' => 1]),
                'points_required' => 0,
            ],
            [
                'name' => 'Vigilant Guardian',
                'description' => 'Reported 5 environmental incidents',
                'icon' => 'bi-eye',
                'color' => '#007bff',
                'criteria' => json_encode(['incidents_reported' => 5]),
                'points_required' => 50,
            ],
            [
                'name' => 'Community Guardian',
                'description' => 'Reported 10 environmental incidents',
                'icon' => 'bi-shield-check',
                'color' => '#007bff',
                'criteria' => json_encode(['incidents_reported' => 10]),
                'points_required' => 100,
            ],
            [
                'name' => 'Environmental Champion',
                'description' => 'Reported 25 environmental incidents',
                'icon' => 'bi-trophy',
                'color' => '#ffc107',
                'criteria' => json_encode(['incidents_reported' => 25]),
                'points_required' => 250,
            ],
            [
                'name' => 'Forum Newcomer',
                'description' => 'Posted your first forum topic',
                'icon' => 'bi-chat-square-text',
                'color' => '#6f42c1',
                'criteria' => json_encode(['forum_topics' => 1]),
                'points_required' => 0,
            ],
            [
                'name' => 'Forum Contributor',
                'description' => 'Started 5 forum discussions',
                'icon' => 'bi-chat-dots',
                'color' => '#6f42c1',
                'criteria' => json_encode(['forum_topics' => 5]),
                'points_required' => 50,
            ],
            [
                'name' => 'Discussion Leader',
                'description' => 'Started 15 forum discussions',
                'icon' => 'bi-megaphone',
                'color' => '#dc3545',
                'criteria' => json_encode(['forum_topics' => 15]),
                'points_required' => 150,
            ],
            [
                'name' => 'Helpful Member',
                'description' => 'Posted 25 forum replies',
                'icon' => 'bi-hand-thumbs-up',
                'color' => '#17a2b8',
                'criteria' => json_encode(['forum_replies' => 25]),
                'points_required' => 125,
            ],
            [
                'name' => 'Event Organizer',
                'description' => 'Organized your first community event',
                'icon' => 'bi-calendar-event',
                'color' => '#fd7e14',
                'criteria' => json_encode(['events_organized' => 1]),
                'points_required' => 25,
            ],
            [
                'name' => 'Event Master',
                'description' => 'Organized 5 community events',
                'icon' => 'bi-calendar-check',
                'color' => '#fd7e14',
                'criteria' => json_encode(['events_organized' => 5]),
                'points_required' => 125,
            ],
            [
                'name' => 'Community Participant',
                'description' => 'Attended 3 community events',
                'icon' => 'bi-people',
                'color' => '#28a745',
                'criteria' => json_encode(['events_attended' => 3]),
                'points_required' => 45,
            ],
            [
                'name' => 'Active Participant',
                'description' => 'Attended 10 community events',
                'icon' => 'bi-star',
                'color' => '#ffc107',
                'criteria' => json_encode(['events_attended' => 10]),
                'points_required' => 150,
            ],
            [
                'name' => 'Volunteer Spirit',
                'description' => 'Applied for your first volunteer opportunity',
                'icon' => 'bi-heart',
                'color' => '#dc3545',
                'criteria' => json_encode(['volunteer_applications' => 1]),
                'points_required' => 10,
            ],
            [
                'name' => 'Volunteer Hero',
                'description' => 'Applied for 5 volunteer opportunities',
                'icon' => 'bi-heart-fill',
                'color' => '#dc3545',
                'criteria' => json_encode(['volunteer_applications' => 5]),
                'points_required' => 75,
            ],
            [
                'name' => 'Dedicated Volunteer',
                'description' => 'Applied for 15 volunteer opportunities',
                'icon' => 'bi-patch-check',
                'color' => '#6f42c1',
                'criteria' => json_encode(['volunteer_applications' => 15]),
                'points_required' => 225,
            ],
            [
                'name' => 'Point Collector',
                'description' => 'Earned 100 community points',
                'icon' => 'bi-gem',
                'color' => '#17a2b8',
                'criteria' => json_encode(['total_points' => 100]),
                'points_required' => 100,
            ],
            [
                'name' => 'Point Master',
                'description' => 'Earned 500 community points',
                'icon' => 'bi-diamond',
                'color' => '#6f42c1',
                'criteria' => json_encode(['total_points' => 500]),
                'points_required' => 500,
            ],
            [
                'name' => 'Eco Champion',
                'description' => 'Earned 1000 community points',
                'icon' => 'bi-trophy-fill',
                'color' => '#ffc107',
                'criteria' => json_encode(['total_points' => 1000]),
                'points_required' => 1000,
            ],
            [
                'name' => 'Environmental Legend',
                'description' => 'Earned 2500 community points',
                'icon' => 'bi-crown',
                'color' => '#fd7e14',
                'criteria' => json_encode(['total_points' => 2500]),
                'points_required' => 2500,
            ],
            [
                'name' => 'Problem Solver',
                'description' => 'Had 5 incidents marked as resolved',
                'icon' => 'bi-check-circle',
                'color' => '#28a745',
                'criteria' => json_encode(['incidents_resolved' => 5]),
                'points_required' => 100,
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::firstOrCreate(
                ['name' => $badgeData['name']],
                $badgeData
            );
        }

        $this->command->info('Community engagement data seeded successfully!');
    }
}
