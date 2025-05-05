<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class EventController extends Controller
{

    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user'];

    public function index()
    {
        Gate::authorize('viewAny', Event::class);
        $query = $this->loadRelationships(Event::query());

        return EventResource::collection(
            $query->latest()->paginate()
        );
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Event::class);
        $event = Event::create([
            ... $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time'
            ]),
            'user_id' => $request->user()->id
        ]);

        return new EventResource($this->loadRelationships($event));
    }


    public function show(Event $event)
    {
        Gate::authorize('view', $event);
        return new EventResource(
            $this->loadRelationships($event)
        );
    }

    public function update(Request $request, Event $event)
    {
        Gate::authorize('update', $event);
        Gate::authorize('update', $event);
        $event->update($request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes |date|after:start_time'
           ])
        );

        return new EventResource($this->loadRelationships($event));
    }

    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);
        $event->delete();

        return response(status: 204);
    }
}
