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
     * @return Entity[]
     */
    public function page(int $page, int $perPage): array;

    /**
     * @param int $id
     * @return Entity|null
     */
    public function find(int $id): ?Entity;

    /**
     * @param string $attribute
     * @param string $operator
     * @param mixed $value
     */
    public function constrain(string $attribute, string $operator, mixed $value): void;
}
