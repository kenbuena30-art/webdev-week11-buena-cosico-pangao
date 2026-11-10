<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        DB::enableQueryLog();

        // FIXED VERSION: eager loading + count
        $events = Event::with('venue')
            ->withCount('participants')
            ->orderBy('id')
            ->limit(50)
            ->get();

        $queries = DB::getQueryLog();

        return view('events.index', [
            'events' => $events,
            'queryCount' => count($queries),
        ]);
    }
}