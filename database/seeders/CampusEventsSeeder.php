<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusEventsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Venues
        $venueData = [
            ['Main Auditorium', 'Building A', 300],
            ['Computer Lab 2', 'Building B', 40],
            ['Covered Court', 'Sports Complex', 500],
            ['Library Hall', 'Building C', 120],
            ['Science Room 1', 'Building D', 60],
            ['Open Garden', 'Campus Grounds', 250],
            ['Student Center', 'Building E', 150],
            ['Art Studio', 'Building F', 35],
        ];

        foreach ($venueData as [$name, $address, $capacity]) {
            Venue::create(['name' => $name, 'address' => $address, 'capacity' => $capacity]);
        }
        $venueIds = Venue::pluck('id')->all();

        // Participants (250, inserted in chunks)
        $firstNames = ['Ana', 'Ben', 'Carla', 'Dan', 'Ella', 'Franco', 'Gina', 'Hugo', 'Iris', 'Jon'];
        $lastNames = ['Reyes', 'Cruz', 'Santos', 'Lopez', 'Garcia', 'Diaz', 'Torres', 'Ramos', 'Flores', 'Bautista'];
        $rows = [];
        for ($i = 1; $i <= 250; $i++) {
            $rows[] = [
                'name' => $firstNames[$i % 10] . ' ' . $lastNames[intdiv($i, 10) % 10],
                'email' => "participant{$i}@campus.test",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('participants')->insert($chunk);
        }
        $participantIds = DB::table('participants')->pluck('id')->all();

        // Description building blocks
        $openers = ['Join us for', 'Come and experience', 'A hands-on session about', 'An open discussion on', 'A friendly gathering focused on', 'A student-led talk about'];
        $topics = ['web development', 'database design', 'campus life', 'career planning', 'research methods', 'team sports', 'creative writing', 'community service', 'robotics', 'public speaking'];
        $audiences = ['for first-year students.', 'for all departments.', 'for curious beginners.', 'for senior students.', 'for student organizations.', 'for faculty and staff.'];
        $closers = ['Snacks will be provided.', 'Bring a friend.', 'Seats are limited.', 'Open to everyone on campus.', 'Certificates will be given.', 'Come early for the best seats.'];
        $types = ['Workshop', 'Talk', 'Forum', 'Meetup', 'Seminar', 'Showcase'];

        // Details building blocks
        $dressCodes = ['casual', 'smart casual', 'formal', 'school uniform'];
        $tagPool = ['free', 'outdoor', 'indoor', 'food', 'networking', 'beginner'];

        // Events (20,000, inserted in chunks of 1,000)
        $rows = [];
        for ($i = 1; $i <= 20000; $i++) {
            $topic = $topics[array_rand($topics)];

            $description = $openers[array_rand($openers)] . ' ' . $topic . ' '
                . $audiences[array_rand($audiences)] . ' ' . $closers[array_rand($closers)];

            // Rare word: "hackathon" appears in only 0.5% of events
            if ($i % 200 === 0) {
                $description .= ' Includes a hackathon challenge.';
            }

            // Rare value: dress_code "costume" appears in only 0.4% of events
            $dressCode = ($i % 250 === 0) ? 'costume' : $dressCodes[array_rand($dressCodes)];

            $details = [
                'dress_code' => $dressCode,
                'capacity' => mt_rand(20, 500),
                'tags' => array_slice($tagPool, mt_rand(0, 3), 2),
            ];

            $rows[] = [
                'venue_id' => $venueIds[array_rand($venueIds)],
                'title' => ucfirst($topic) . ' ' . $types[array_rand($types)] . ' #' . $i,
                'description' => $description,
                'starts_at' => $now->copy()->addDays(mt_rand(-180, 180))->setTime(mt_rand(8, 18), 0)->toDateTimeString(),
                'details' => json_encode($details),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) === 1000) {
                DB::table('events')->insert($rows);
                $rows = [];
            }
        }

        // Registrations (first 150 events get 3 participants each)
        $eventIds = DB::table('events')->orderBy('id')->limit(150)->pluck('id');
        $pivotRows = [];
        foreach ($eventIds as $eventId) {
            foreach (collect($participantIds)->random(3) as $participantId) {
                $pivotRows[] = [
                    'event_id' => $eventId,
                    'participant_id' => $participantId,
                    'status' => mt_rand(1, 4) === 1 ? 'waitlisted' : 'registered',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        foreach (array_chunk($pivotRows, 200) as $chunk) {
            DB::table('event_participant')->insert($chunk);
        }

        // Notes
        $firstEvent = Event::orderBy('id')->first();
        $firstEvent->notes()->create(['body' => 'Bring student ID.']);
        $firstEvent->notes()->create(['body' => 'Registration opens at 8 AM.']);
        Venue::orderBy('id')->first()->notes()->create(['body' => 'Projector needs a technician.']);
    }
}