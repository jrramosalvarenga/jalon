<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\RideRequest;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ana = User::factory()->create([
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
        ]);

        $beto = User::factory()->create([
            'name' => 'Beto Gómez',
            'email' => 'beto@example.com',
        ]);

        $carla = User::factory()->create([
            'name' => 'Carla Ruiz',
            'email' => 'carla@example.com',
        ]);

        // Ana and Beto are accepted contacts.
        Contact::create(['user_id' => $ana->id, 'contact_id' => $beto->id, 'status' => 'accepted']);

        // Ana and Carla are accepted contacts.
        Contact::create(['user_id' => $carla->id, 'contact_id' => $ana->id, 'status' => 'accepted']);

        // Beto has a pending request to Carla.
        Contact::create(['user_id' => $beto->id, 'contact_id' => $carla->id, 'status' => 'pending']);

        // Ana offers a paid trip with 3 seats.
        $tripAna = Trip::create([
            'driver_id' => $ana->id,
            'origin' => 'Tegucigalpa',
            'destination' => 'San Pedro Sula',
            'departure_at' => now()->addDays(2)->setTime(7, 0),
            'seats_total' => 3,
            'seats_available' => 3,
            'cost' => 150,
            'notes' => 'Salgo desde Mall Multiplaza.',
            'status' => 'active',
        ]);

        // Beto offers a free trip with 2 seats.
        Trip::create([
            'driver_id' => $beto->id,
            'origin' => 'Tegucigalpa',
            'destination' => 'Comayagua',
            'departure_at' => now()->addDays(1)->setTime(17, 30),
            'seats_total' => 2,
            'seats_available' => 2,
            'cost' => null,
            'notes' => null,
            'status' => 'active',
        ]);

        // Carla requests a seat on Ana's trip.
        RideRequest::create([
            'trip_id' => $tripAna->id,
            'requester_id' => $carla->id,
            'seats_requested' => 1,
            'status' => 'pending',
            'message' => '¿Hay espacio para una maleta grande?',
        ]);
    }
}
