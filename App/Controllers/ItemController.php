<?php

namespace App\Controllers;

use App\Repositories\ItemRepository;

class ItemController extends CrudController
{
    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
    }
}
