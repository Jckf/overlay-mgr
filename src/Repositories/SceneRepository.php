<?php

namespace OverlayMgr\Repositories;

class SceneRepository extends MySqlRepository
{
    public function test()
    {
        $query = $this->getPdo()->prepare('SELECT * FROM scenes');
        $query->execute();
        print_r($query->fetchAll());
    }
}
