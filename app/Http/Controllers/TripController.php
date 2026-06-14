<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $myTrips = Trip::where('driver_id', $user->id)
            ->orderBy('departure_at')
            ->get();

        $feed = Trip::visibleTo($user)
            ->where('driver_id', '!=', $user->id)
            ->upcoming()
            ->with('driver')
            ->orderBy('departure_at')
            ->get();

        return view('trips.index', [
            'myTrips' => $myTrips,
            'feed' => $feed,
        ]);
    }

    public function create()
    {
        return view('trips.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTrip($request);
        $validated['driver_id'] = $request->user()->id;
        $validated['seats_available'] = $validated['seats_total'];
        $validated['status'] = 'active';

        $trip = Trip::create($validated);

        return redirect()->route('trips.show', $trip)->with('status', 'Ruta publicada.');
    }

    public function show(Request $request, Trip $trip)
    {
        $user = $request->user();

        abort_unless($trip->driver_id === $user->id || $user->isContactOf($trip->driver), 403);

        $trip->load('driver');

        $rideRequests = null;
        $myRequest = null;

        if ($trip->driver_id === $user->id) {
            $rideRequests = $trip->rideRequests()->with('requester')->latest()->get();
        } else {
            $myRequest = $trip->rideRequests()->where('requester_id', $user->id)->first();
        }

        return view('trips.show', [
            'trip' => $trip,
            'rideRequests' => $rideRequests,
            'myRequest' => $myRequest,
        ]);
    }

    public function edit(Request $request, Trip $trip)
    {
        abort_unless($trip->driver_id === $request->user()->id, 403);

        return view('trips.edit', ['trip' => $trip]);
    }

    public function update(Request $request, Trip $trip): RedirectResponse
    {
        abort_unless($trip->driver_id === $request->user()->id, 403);

        $validated = $this->validateTrip($request);

        $acceptedSeats = $trip->seats_total - $trip->seats_available;
        $validated['seats_available'] = max(0, $validated['seats_total'] - $acceptedSeats);

        $trip->update($validated);

        return redirect()->route('trips.show', $trip)->with('status', 'Ruta actualizada.');
    }

    public function destroy(Request $request, Trip $trip): RedirectResponse
    {
        abort_unless($trip->driver_id === $request->user()->id, 403);

        $trip->delete();

        return redirect()->route('trips.index')->with('status', 'Ruta eliminada.');
    }

    private function validateTrip(Request $request): array
    {
        return $request->validate([
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'origin_lat' => ['nullable', 'numeric'],
            'origin_lng' => ['nullable', 'numeric'],
            'destination_lat' => ['nullable', 'numeric'],
            'destination_lng' => ['nullable', 'numeric'],
            'departure_at' => ['required', 'date'],
            'seats_total' => ['required', 'integer', 'min:1', 'max:50'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
