<?php

namespace OverlayMgr\Repositories;

use PDO;

class MySqlRepository implements Repository
{
    /** @var PDO */
    protected static $pdo;

    /**
     * @param PDO $pdo
     */
    public static function setPdo(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    /**
     * @return PDO
     */
    protected function getPdo()
    {
        return self::$pdo;
    }
}
