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