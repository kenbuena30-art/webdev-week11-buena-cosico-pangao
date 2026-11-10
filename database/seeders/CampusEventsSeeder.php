<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Note;
use App\Models\Participant;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class CampusEventsSeeder extends Seeder
{
    public function run(): void
    {
        // Venues
        $hall = Venue::create(['name' => 'Main Auditorium', 'address' => 'Building A', 'capacity' => 300]);
        $lab = Venue::create(['name' => 'Computer Lab 2', 'address' => 'Building B', 'capacity' => 40]);
        $court = Venue::create(['name' => 'Covered Court', 'address' => 'Sports Complex', 'capacity' => 500]);

        // Events
        $e1 = $hall->events()->create(['title' => 'Orientation Day', 'description' => 'Welcome for new students', 'starts_at' => now()->addDays(3)]);
        $e2 = $hall->events()->create(['title' => 'Career Talk', 'description' => 'Industry speakers', 'starts_at' => now()->addDays(7)]);
        $e3 = $hall->events()->create(['title' => 'Research Forum', 'description' => 'Student research showcase', 'starts_at' => now()->addDays(10)]);
        $e4 = $lab->events()->create(['title' => 'Laravel Workshop', 'description' => 'Hands-on session', 'starts_at' => now()->addDays(5)]);
        $e5 = $lab->events()->create(['title' => 'Database Bootcamp', 'description' => 'PostgreSQL basics', 'starts_at' => now()->addDays(12)]);
        $e6 = $court->events()->create(['title' => 'Intramurals Opening', 'description' => 'Sports festival opening', 'starts_at' => now()->addDays(20)]);

        // Participants
        $p = [];
        foreach ([
            ['Ana Reyes', 'ana@campus.test'],
            ['Ben Cruz', 'ben@campus.test'],
            ['Carla Santos', 'carla@campus.test'],
            ['Dan Lopez', 'dan@campus.test'],
            ['Ella Garcia', 'ella@campus.test'],
            ['Franco Diaz', 'franco@campus.test'],
            ['Gina Torres', 'gina@campus.test'],
            ['Hugo Ramos', 'hugo@campus.test'],
        ] as $i => [$name, $email]) {
            $p[$i + 1] = Participant::create(['name' => $name, 'email' => $email]);
        }

        // Registrations (12 total: 9 registered, 3 waitlisted)
        $e1->participants()->attach([
            $p[1]->id => ['status' => 'registered'],
            $p[2]->id => ['status' => 'registered'],
            $p[3]->id => ['status' => 'waitlisted'],
        ]);
        $e2->participants()->attach([
            $p[1]->id => ['status' => 'registered'],
            $p[4]->id => ['status' => 'registered'],
        ]);
        $e3->participants()->attach([
            $p[2]->id => ['status' => 'registered'],
            $p[5]->id => ['status' => 'registered'],
            $p[6]->id => ['status' => 'waitlisted'],
        ]);
        $e4->participants()->attach([
            $p[3]->id => ['status' => 'registered'],
            $p[7]->id => ['status' => 'registered'],
        ]);
        $e5->participants()->attach([
            $p[8]->id => ['status' => 'registered'],
            $p[4]->id => ['status' => 'waitlisted'],
        ]);
        // $e6 intentionally has no registrations

        // Notes (on both events and venues)
        $e1->notes()->create(['body' => 'Bring student ID.']);
        $e1->notes()->create(['body' => 'Registration opens at 8 AM.']);
        $e4->notes()->create(['body' => 'Bring your own laptop.']);
        $hall->notes()->create(['body' => 'Projector needs a technician.']);
        $lab->notes()->create(['body' => 'Maximum of 40 seats.']);
        $court->notes()->create(['body' => 'Closed when it rains.']);
    }
}