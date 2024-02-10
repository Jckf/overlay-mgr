<?php

namespace App\Repositories;

use App\Entities\Item;

class ItemRepository extends MySqlRepository
{
    protected string $table = 'items';

    protected string $entityClass = Item::class;
}
