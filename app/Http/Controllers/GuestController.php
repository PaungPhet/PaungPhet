<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{

    public function show(string $weddingSlug, string $guestSlug)
    {
        $guest = Guest::where('slug', $guestSlug)
            ->with('wedding')
            ->firstOrFail();

        // Ensure the wedding slug matches
        if ($guest->wedding->slug !== $weddingSlug) {
            abort(404);
        }

        // Update guest status if pending
        if ($guest->status === 'pending') {
            $guest->status = 'seen';
            $guest->save();
        }

        return view("themes.default", [
            'wedding' => $guest->wedding,
            'guest' => $guest
        ]);
    }

    public function submitNote(Request $request, string $weddingSlug, string $guestSlug)
    {
        $guest = Guest::where('slug', $guestSlug)
            ->with('wedding')
            ->firstOrFail();

        // Ensure the wedding slug matches
        if ($guest->wedding->slug !== $weddingSlug) {
            abort(404);
        }

        // Ensure the guest is notable
        if (!$guest->is_notable) {
            abort(403);
        }

        $note = $request->input('note', '');
        $guest->note = $note;
        $guest->save();

        return redirect()->route('guests.show', ['weddingSlug' => $weddingSlug, 'guestSlug' => $guestSlug])
            ->with('success', 'Note has been sent successfully.');
    }


}
