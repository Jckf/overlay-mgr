<?php

namespace App\Repositories;

use App\Entities\Entity;

interface Repository
{
    /**
     * @param Entity $entity
     * @return bool
     */
    public function save(Entity $entity): bool;

    /**
     * @param Entity $entity
     * @return bool
     */
    public function delete(Entity $entity): bool;

    /**
     * @param int $page
     * @param int $perPage
     * @param string $orderBy
     * @param string $direction
     * @return Entity[]
     */
    public function page(int $page, int $perPage, string $orderBy = 'id', string $direction = 'asc'): array;

    /**
     * @param int $id
     * @return Entity|null
     */
    public function find(int $id): ?Entity;

    /**
     * @param string $attribute
     * @param string $operator
     * @param mixed $value
     * @deprecated This gives the repository state, which will leak between requests.
     * @todo Redesign to get rid of state.
     */
    public function constrain(string $attribute, string $operator, mixed $value): void;
}
