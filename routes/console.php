<?php

use Illuminate\Support\Facades\Schedule;

// ── Abandoned-cart recovery ──
// The command itself checks the abandoned_cart_enabled setting, so the
// schedule stays unconditional and admins toggle behavior in Settings → Notification.
Schedule::command('carts:send-reminders')->everyThirtyMinutes()->withoutOverlapping();
