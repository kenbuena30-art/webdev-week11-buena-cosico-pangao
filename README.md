# Campus Events: Relational Data Model (Laravel + PostgreSQL)

A small Laravel app that models campus venues, events, participants and notes, with one-to-many, many-to-many and polymorphic relationships.

## Setup

1. `composer install`
2. Copy `.env.example` to `.env` and set the PostgreSQL settings (`DB_CONNECTION=pgsql`, `DB_DATABASE=campus_events`, and your own username and password)
3. `php artisan key:generate`
4. `php artisan migrate:fresh --seed`
5. `php artisan serve`, then open `/report`

## Relationships

| Link | Type | Table holding the foreign key | Eloquent methods | ON DELETE | Reason |
|------|------|-------------------------------|------------------|-----------|--------|
| Venue → Event | One-to-many | `events.venue_id` | `Venue::events()` (hasMany), `Event::venue()` (belongsTo) | RESTRICT | A venue that still has events must not be deleted, so the database blocks it. |
| Event ↔ Participant | Many-to-many | `event_participant` (pivot: `event_id`, `participant_id`, `status`) | `Event::participants()` and `Participant::events()` (belongsToMany with `withPivot('status')`) | CASCADE (both sides) | Registrations mean nothing without their event or participant, so they are removed automatically. |
| Note → Event or Venue | Polymorphic one-to-many | `notes.notable_id` + `notes.notable_type` | `Note::notable()` (morphTo), `Event::notes()` and `Venue::notes()` (morphMany) | None (no foreign key possible) | A note must be able to belong to an event or a venue, so one foreign key can't be used. |

## Why PostgreSQL cannot enforce the polymorphic link

A foreign key can reference only one specific table. `notes.notable_id` can point to either `events` or `venues`, and `notable_type` is just text, so PostgreSQL has nothing to check. The database will accept a note that points to a record that doesn't exist, and the Laravel code has to keep these records valid.

## Rules enforced by PostgreSQL

- An event cannot be created with a venue that doesn't exist (foreign key).
- A venue with events cannot be deleted (`ON DELETE RESTRICT`).
- The same participant cannot register for the same event twice (composite primary key on `event_id`, `participant_id`).
- Deleting an event or participant removes its registrations (`ON DELETE CASCADE`).

# Week 11: Optimized & Advanced Queries (Campus Events at Scale)

The seeder now creates 20,000 events, 250 participants and 150 registrations. Run `php artisan migrate:fresh --seed` to rebuild everything, including the indexes.

## Query counts for /events (50 events)

| Version | Queries run | What happens |
|---------|-------------|--------------|
| Naive (lazy loading) | 101 | 1 query for the events, 50 for venues, 50 for participant counts |
| Fixed (`with('venue')` + `withCount('participants')`) | 2 | 1 query for the events and counts, 1 for all venues |

The naive version runs one extra query per event (the N+1 problem). The fix loads all venues in a single query and counts participants inside the main query, so the number of queries no longer depends on the number of events.

## Query plans (EXPLAIN ANALYZE)

| # | Query | Index | Plan used | Execution time |
|---|-------|-------|-----------|----------------|
| 1 | JSONB arrow: `details->>'dress_code' = 'costume'` | None | Seq Scan | 9.654 ms |
| 1 | JSONB arrow | Expression index `events_dress_code_idx` | Bitmap Index Scan | 1.072 ms |
| 2 | `whereJsonContains`: `details @> '{"dress_code":"costume"}'` | `events_dress_code_idx` exists but is not usable | Seq Scan | 8.219 ms |
| 2 | `whereJsonContains` | GIN index `events_details_gin_idx` | Bitmap Index Scan | 0.340 ms |
| 3 | Full-text: `to_tsvector('english', description) @@ plainto_tsquery('english', 'hackathon')` | None | Seq Scan | 288.779 ms |
| 3 | Full-text | GIN index `events_description_fts_idx` | Bitmap Index Scan | 0.333 ms |

**Index exists but is not used:** In row 2, `events_dress_code_idx` exists but PostgreSQL ignored it. That index is built on the expression `details->>'dress_code'`, and the containment query uses the `@>` operator, which that index cannot answer. Only the GIN index on the whole `details` column can.

## Step 8: Where the indexes live

All indexes are created in the migration `add_indexes_to_events_table`, so they survive `php artisan migrate:fresh --seed`. The GIN index and the full-text index use Laravel's schema builder. The expression index on `details->>'dress_code'` uses a raw SQL statement inside the migration, because Laravel's schema builder can only index plain columns, not an expression.

## Step 9: Choosing columns

- **Stored in the JSONB `details` column: `dress_code`.** It is optional extra information that can differ between events, and JSONB lets us add new keys later without changing the table.
- **Stored in a normal column: `starts_at`.** Every event has a start date, and we sort and filter by it often. A normal timestamp column keeps the correct date type and works well with ordinary indexes.

## Scopes on the Event model

| Scope | What it does |
|-------|--------------|
| `upcoming()` | Keeps only events that have not started yet |
| `atVenue($venueId)` | Keeps only events at one venue (takes a parameter) |
| `dressCodeArrow($dressCode)` | JSONB filter using the arrow form (`details->dress_code`) |
| `dressCodeContains($dressCode)` | JSONB filter using `whereJsonContains` on the whole column |