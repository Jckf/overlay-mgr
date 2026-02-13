<?php

namespace App\Controllers;

use App\Repositories\BidRepository;
use App\Request;
use Psr\Http\Message\ResponseInterface;

class ItemBidController extends CrudController
{
    public function __construct(BidRepository $repository)
    {
        $this->repository = $repository;
    }

    public function bidIndex(Request $request, int $itemId): ResponseInterface
    {
        $constraintId = $this->repository->constrain('itemId', '=', $itemId);

        try {
            return response()->json(
                $this->repository->page(
                    $request->get('page', 1),
                    $request->get('limit', 10),
                    'amount',
                )
            );
        } finally {
            $this->repository->removeConstraint($constraintId);
        }
    }
}
