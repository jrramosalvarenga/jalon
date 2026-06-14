<?php

namespace App\Http\Controllers;

use App\Models\RideRequest;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RideRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $received = RideRequest::whereHas('trip', fn ($query) => $query->where('driver_id', $user->id))
            ->with(['trip', 'requester'])
            ->latest()
            ->get();

        $sent = RideRequest::where('requester_id', $user->id)
            ->with(['trip.driver'])
            ->latest()
            ->get();

        return view('ride-requests.index', [
            'received' => $received,
            'sent' => $sent,
        ]);
    }

    public function store(Request $request, Trip $trip): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->isContactOf($trip->driver), 403);

        if ($trip->driver_id === $user->id) {
            throw ValidationException::withMessages([
                'trip' => 'No puedes solicitar un jalón en tu propia ruta.',
            ]);
        }

        if ($trip->status !== 'active') {
            throw ValidationException::withMessages([
                'trip' => 'Esta ruta ya no está activa.',
            ]);
        }

        if ($trip->rideRequests()->where('requester_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'trip' => 'Ya enviaste una solicitud para esta ruta.',
            ]);
        }

        $validated = $request->validate([
            'seats_requested' => ['required', 'integer', 'min:1', 'max:'.$trip->seats_available],
            'message' => ['nullable', 'string', 'max:255'],
        ]);

        RideRequest::create([
            'trip_id' => $trip->id,
            'requester_id' => $user->id,
            'seats_requested' => $validated['seats_requested'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('trips.show', $trip)->with('status', 'Solicitud de jalón enviada.');
    }

    public function update(Request $request, RideRequest $rideRequest): RedirectResponse
    {
        $user = $request->user();
        $trip = $rideRequest->trip;

        $validated = $request->validate([
            'action' => ['required', 'in:accept,reject,cancel'],
        ]);

        if (in_array($validated['action'], ['accept', 'reject'])) {
            abort_unless($trip->driver_id === $user->id, 403);

            if ($validated['action'] === 'accept') {
                if ($rideRequest->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'action' => 'Esta solicitud ya fue procesada.',
                    ]);
                }

                if ($trip->seats_available < $rideRequest->seats_requested) {
                    throw ValidationException::withMessages([
                        'action' => 'No hay suficientes cupos disponibles.',
                    ]);
                }

                $trip->decrement('seats_available', $rideRequest->seats_requested);
                $rideRequest->update(['status' => 'accepted']);
            } else {
                $rideRequest->update(['status' => 'rejected']);
            }
        } else {
            abort_unless($rideRequest->requester_id === $user->id, 403);

            if ($rideRequest->status === 'accepted') {
                $trip->increment('seats_available', $rideRequest->seats_requested);
            }

            $rideRequest->update(['status' => 'cancelled']);
        }

        return redirect()->route('ride-requests.index')->with('status', 'Solicitud actualizada.');
    }
}
