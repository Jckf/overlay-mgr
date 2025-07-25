<?php

namespace App\Controllers;

use App\Repositories\Repository;
use App\Request;
use Psr\Http\Message\ResponseInterface;

abstract class CrudController extends Controller
{
    protected Repository $repository;

    public function index(Request $request): ResponseInterface
    {
        return response()->json(
            $this->repository->page(
                $request->get('page', 1),
                $request->get('limit', 10)
            )
        );
    }

    public function get(Request $request, int $id): ResponseInterface
    {
        return response()->json(
            $this->repository->find($id)
        );
    }
}
