<?php

use Illuminate\Support\Facades\Schedule;

// ── Abandoned-cart recovery ──
// The command itself checks the abandoned_cart_enabled setting, so the
// schedule stays unconditional and admins toggle behavior in Settings → Notification.
Schedule::command('carts:send-reminders')->everyThirtyMinutes()->withoutOverlapping();

// ── Weekly sales summary ──
// Same pattern: the command checks weekly_sales_summary_enabled in settings.
// Mondays 08:00, covering the previous Mon–Sun week.
Schedule::command('reports:send-weekly-summary')->weeklyOn(1, '8:00');

// ── Monthly sales summary ──
// Same pattern: the command checks monthly_sales_summary_enabled in settings.
// On the 1st at 08:00, covering the previous calendar month.
Schedule::command('reports:send-monthly-summary')->monthlyOn(1, '8:00');
