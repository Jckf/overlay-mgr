<?php

use App\Repositories\MySqlRepository;

$pdo = new PDO('mysql:host=' . env('DB_HOST', 'database') . ';port=' . env('DB_PORT', 3306) . ';dbname=' . env('DB_NAME', 'overlay-mgr'), env('DB_USER', 'overlay-mgr'), env('DB_PASS', 'overlay-mgr'));

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

MySqlRepository::setPdo($pdo);
