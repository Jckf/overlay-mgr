<?php

namespace App\Controllers;

use App\Repositories\Repository;
use App\Request;

abstract class CrudController extends Controller
{
    protected Repository $repository;

    public function index(Request $request): void
    {
        header('Content-Type: application/json');

        echo json_encode(
            $this->repository->page(
                $request->get('page', 1),
                $request->get('limit', 10)
            ),
            JSON_PRETTY_PRINT
        );
    }

    public function get(Request $request, int $id): void
    {
        header('Content-Type: application/json');

        echo json_encode(
            $this->repository->find($id),
            JSON_PRETTY_PRINT
        );
    }
}
