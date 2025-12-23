<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function open(DatabaseNotification $notification): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user !== null, 401);

        abort_unless(
            $notification->notifiable_id === $user->getKey()
                && $notification->notifiable_type === get_class($user),
            403
        );

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $link = $notification->data['link'] ?? null;

        if (is_string($link) && $link !== '') {
            return redirect()->to($link);
        }

        return redirect()->route('dashboard');
    }
}
