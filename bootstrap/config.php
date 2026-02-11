<?php

declare(strict_types=1);

use App\Database\Database;
use App\Database\MySqlDatabase;

container()->bind(
    [ Database::class, MySqlDatabase::class ],
    new MySqlDatabase(
        env('DB_HOST', 'database'),
        (int) env('DB_PORT', '3306'),
        env('DB_NAME', 'overlay-mgr'),
        env('DB_USER', 'overlay-mgr'),
        env('DB_PASS', 'overlay-mgr')
    )
);
