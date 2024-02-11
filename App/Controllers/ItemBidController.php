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

        header('Content-Type: application/json');

        echo json_encode(
            $this->repository->page(
                $request->get('page', 1),
                $request->get('limit', 10),
                'amount',
            ),
            JSON_PRETTY_PRINT
        );
    }
}
