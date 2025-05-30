<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CanLoadRelationships;
use App\Http\Resources\AttendeeResource;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttendeeController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user'];

    public function index(Event $event)
    {
        Gate::authorize('viewAny', Attendee::class);
        $attendees = $event->attendees()->latest();
         $attendees = $this->loadRelationships(
             $event->attendees()->latest()
         );

        return AttendeeResource::collection(
            $attendees->paginate()
        );
    }

    public function store(Request $request, Event $event)
    {
        Gate::authorize('create', Attendee::class);
        $attendee = $this->loadRelationships(
            $event->attendees()->create([
                'user_id' => 1
            ])
        );

        return new AttendeeResource($attendee);
    }


    public function show(Event $event, Attendee $attendee)
    {
        Gate::authorize('view', $attendee);
        return new AttendeeResource(
            $this->loadRelationships($attendee)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $event, Attendee $attendee)
    {
        Gate::authorize('delete', $attendee);
        $attendee->delete();

        return response(status: 204);
    }
}
