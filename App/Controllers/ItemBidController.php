<?php

namespace App\Controllers;

use App\Repositories\BidRepository;
use App\Request;

class ItemBidController extends CrudController
{
    public function __construct(BidRepository $repository)
    {
        $this->repository = $repository;
    }

    public function bidIndex(Request $request, int $itemId): void
    {
        $this->repository->constrain('itemId', '=', $itemId);

        parent::index($request);
    }
}
