<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo Mode
    |--------------------------------------------------------------------------
    |
    | A public demo is usually a copy of a real deployment's .env, so the
    | safety of the demo cannot depend on MAIL_MAILER or QUEUE_CONNECTION
    | being set correctly by hand. When this is enabled every mailer is
    | replaced with the log transport and the queue is forced to sync, so a
    | visitor action can never deliver real email. See docs/demo-mode.md.
    |
    */

    'enabled' => (bool) env('DEMO_MODE', false),

];
