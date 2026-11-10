<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        DB::enableQueryLog();

        // NAIVE VERSION: no eager loading
        $events = Event::orderBy('id')->limit(50)->get();

        // Touch the relations so the lazy queries actually run
        foreach ($events as $event) {
            $event->venue->name;
            $event->participants->count();
        }

        $queries = DB::getQueryLog();

        return view('events.index', [
            'events' => $events,
            'queryCount' => count($queries),
        ]);
    }
}