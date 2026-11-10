<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        DB::enableQueryLog();

        $query = Event::with('venue')
            ->withCount('participants')
            ->orderBy('id')
            ->limit(50);

        // JSONB filter: arrow form by default, containment with ?mode=contains
        if ($request->filled('dress_code')) {
            $dressCode = $request->query('dress_code');

            if ($request->query('mode') === 'contains') {
                $query->dressCodeContains($dressCode);
            } else {
                $query->dressCodeArrow($dressCode);
            }
        }

        // SQL that Laravel produces, for the screenshots and README
        $sql = $query->toSql() . '   | bindings: ' . json_encode($query->getBindings());

        $events = $query->get();

        $queries = DB::getQueryLog();

        return view('events.index', [
            'events' => $events,
            'queryCount' => count($queries),
            'sql' => $sql,
        ]);
    }
}