<?php

use OverlayMgr\Repositories\MySqlRepository;

$pdo = new PDO('mysql:host=mysql;port=3306;dbname=overlay-mgr', 'overlay-mgr', 'overlay-mgr');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

MySqlRepository::setPdo($pdo);
