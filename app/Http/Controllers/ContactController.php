<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $contacts = Contact::accepted()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)->orWhere('contact_id', $user->id);
            })
            ->with(['user', 'contact'])
            ->get()
            ->map(fn (Contact $contact) => [
                'record' => $contact,
                'person' => $contact->user_id === $user->id ? $contact->contact : $contact->user,
            ]);

        $received = Contact::pending()
            ->where('contact_id', $user->id)
            ->with('user')
            ->get();

        $sent = Contact::pending()
            ->where('user_id', $user->id)
            ->with('contact')
            ->get();

        return view('contacts.index', [
            'contacts' => $contacts,
            'received' => $received,
            'sent' => $sent,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = $request->user();
        $target = User::where('email', $validated['email'])->first();

        if ($target->id === $user->id) {
            throw ValidationException::withMessages([
                'email' => 'No puedes agregarte a ti mismo como contacto.',
            ]);
        }

        $this->sendContactRequest($user, $target, 'email');

        return redirect()->route('contacts.index')->with('status', 'Solicitud de contacto enviada.');
    }

    /**
     * Send a contact request to another registered user.
     */
    public function requestUser(Request $request, User $user): RedirectResponse
    {
        $from = $request->user();

        if ($user->id === $from->id) {
            return redirect()->route('contacts.index')->with('status', 'No puedes agregarte a ti mismo como contacto.');
        }

        $this->sendContactRequest($from, $user, 'contact');

        return redirect()->route('contacts.index')->with('status', 'Solicitud de contacto enviada.');
    }

    /**
     * Match the user's phone contacts (emails/phones) against registered users.
     */
    public function matchPhoneContacts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'emails' => ['array'],
            'emails.*' => ['string'],
            'phones' => ['array'],
            'phones.*' => ['string'],
        ]);

        $user = $request->user();
        $existingIds = $user->contactIds()->push($user->id);

        $emails = $validated['emails'] ?? [];
        $phones = $validated['phones'] ?? [];

        $matches = User::where(function ($query) use ($emails, $phones) {
            if (! empty($emails)) {
                $query->orWhereIn('email', $emails);
            }
            if (! empty($phones)) {
                $query->orWhereIn('phone', $phones);
            }
        })
            ->whereNotIn('id', $existingIds)
            ->get(['id', 'name', 'email']);

        return response()->json(['matches' => $matches]);
    }

    /**
     * Show Facebook friends who are also Jalon users.
     */
    public function facebookFriends(Request $request)
    {
        $user = $request->user();

        if (! $user->facebook_token) {
            return redirect()->route('contacts.index')->with('status', 'Vincula tu cuenta de Facebook para buscar amigos.');
        }

        $response = Http::get('https://graph.facebook.com/v19.0/me/friends', [
            'access_token' => $user->facebook_token,
        ]);

        $friendIds = collect($response->json('data', []))->pluck('id');

        $existingIds = $user->contactIds()->push($user->id);

        $friends = User::whereIn('facebook_id', $friendIds)
            ->whereNotIn('id', $existingIds)
            ->get();

        return view('contacts.facebook-friends', ['friends' => $friends]);
    }

    public function accept(Request $request, Contact $contact): RedirectResponse
    {
        abort_unless($contact->contact_id === $request->user()->id, 403);

        $contact->update(['status' => 'accepted']);

        return redirect()->route('contacts.index')->with('status', 'Contacto aceptado.');
    }

    public function reject(Request $request, Contact $contact): RedirectResponse
    {
        abort_unless($contact->contact_id === $request->user()->id, 403);

        $contact->delete();

        return redirect()->route('contacts.index')->with('status', 'Solicitud rechazada.');
    }

    public function destroy(Request $request, Contact $contact): RedirectResponse
    {
        $userId = $request->user()->id;

        abort_unless($contact->user_id === $userId || $contact->contact_id === $userId, 403);

        $contact->delete();

        return redirect()->route('contacts.index')->with('status', 'Contacto eliminado.');
    }

    /**
     * Create a pending contact request between two users, validating duplicates.
     */
    private function sendContactRequest(User $from, User $to, string $field = 'email'): void
    {
        $existing = Contact::where(function ($query) use ($from, $to) {
            $query->where('user_id', $from->id)->where('contact_id', $to->id);
        })->orWhere(function ($query) use ($from, $to) {
            $query->where('user_id', $to->id)->where('contact_id', $from->id);
        })->first();

        if ($existing) {
            throw ValidationException::withMessages([
                $field => 'Ya existe una relación de contacto con este usuario.',
            ]);
        }

        Contact::create([
            'user_id' => $from->id,
            'contact_id' => $to->id,
            'status' => 'pending',
        ]);
    }
}
