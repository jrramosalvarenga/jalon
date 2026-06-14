<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'facebook_id',
        'facebook_token',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'facebook_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Contact records this user initiated.
     */
    public function contactsInitiated(): HasMany
    {
        return $this->hasMany(Contact::class, 'user_id');
    }

    /**
     * Contact records where this user was invited.
     */
    public function contactsReceived(): HasMany
    {
        return $this->hasMany(Contact::class, 'contact_id');
    }

    /**
     * Trips this user offers as a driver.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    /**
     * Ride requests this user has sent as a passenger.
     */
    public function rideRequests(): HasMany
    {
        return $this->hasMany(RideRequest::class, 'requester_id');
    }

    /**
     * Pending contact requests sent to this user.
     */
    public function pendingContactRequests(): HasMany
    {
        return $this->contactsReceived()->where('status', 'pending');
    }

    /**
     * The IDs of users that are accepted contacts of this user, in either direction.
     */
    public function contactIds(): Collection
    {
        return Contact::where('status', 'accepted')
            ->where(function ($query) {
                $query->where('user_id', $this->id)
                    ->orWhere('contact_id', $this->id);
            })
            ->get()
            ->map(fn (Contact $contact) => $contact->user_id === $this->id ? $contact->contact_id : $contact->user_id);
    }

    /**
     * The accepted contacts of this user.
     */
    public function contacts(): Collection
    {
        return User::whereIn('id', $this->contactIds())->get();
    }

    /**
     * Whether the given user is an accepted contact of this user.
     */
    public function isContactOf(User $user): bool
    {
        return $this->contactIds()->contains($user->id);
    }
}
