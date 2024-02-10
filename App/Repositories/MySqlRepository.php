<?php

namespace App\Repositories;

use App\Entities\Entity;
use PDO;

class MySqlRepository implements Repository
{
    /** @var PDO */
    protected static PDO $pdo;

    /**
     * @param PDO $pdo
     */
    public static function setPdo(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    /** @var string */
    protected string $table;

    /** @var class-string<Entity> */
    protected string $entityClass;

    /**
     * @return PDO
     */
    protected function getPdo(): PDO
    {
        return self::$pdo;
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    protected function saveInsert(Entity $entity): bool
    {
        $columns = [];
        $placeholders = [];
        $values = [];

        foreach ($entity->getAttributes() as $attribute) {
            $columns[] = snake_case($attribute);
            $placeholders[] = ':' . $attribute;
            $values[':' . $attribute] = $entity->getAttribute($attribute);
        }

        $columnsString = implode(', ', $columns);
        $placeholdersString = implode(', ', $placeholders);

        $statement = $this->getPdo()->prepare("INSERT INTO {$this->table} ({$columnsString}) VALUES ({$placeholdersString})");

        if (!$statement->execute($values)) {
            return false;
        }

        $entity->setAttribute($entity->getPrimaryKeyName(), $this->getPdo()->lastInsertId());

        return true;
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    protected function saveUpdate(Entity $entity): bool
    {
        $primaryKey = $entity->getPrimaryKeyName();
        $primaryKeyColumn = snake_case($primaryKey);

        $columns = [];
        $values = [];

        foreach ($entity->getAttributes() as $attribute) {
            if ($attribute !== $entity->getPrimaryKeyName()) {
                $columns[] = snake_case($attribute) . ' = :' . $attribute;
            }

            $values[':' . $attribute] = $entity->getAttribute($attribute);
        }

        $columnsString = implode(', ', $columns);

        $statement = $this->getPdo()->prepare("UPDATE {$this->table} SET {$columnsString} WHERE {$primaryKeyColumn} = :{$primaryKey}");
        return $statement->execute($values);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function save(Entity $entity): bool
    {
        if ($entity->exists()) {
            return $this->saveUpdate($entity);
        }

        return $this->saveInsert($entity);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function delete(Entity $entity): bool
    {
        $primaryKey = $entity->getPrimaryKeyName();
        $primaryKeyColumn = snake_case($primaryKey);

        $statement = $this->getPdo()->prepare("DELETE FROM {$this->table} WHERE {$primaryKeyColumn} = :{$primaryKey}");
        return $statement->execute([
            ':' . $primaryKey => $entity->getAttribute($primaryKey),
        ]);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Entity[]
     */
    public function page(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        $statement = $this->getPdo()->prepare("SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset");
        $statement->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, $this->entityClass);
    }

    /**
     * @param int $id
     * @return Entity
     */
    public function find(int $id): Entity
    {
        $primaryKey = (new $this->entityClass())->getPrimaryKeyName();
        $primaryKeyColumn = snake_case($primaryKey);

        $statement = $this->getPdo()->prepare("SELECT * FROM {$this->table} WHERE {$primaryKeyColumn} = :{$primaryKey}");
        $statement->execute([
            ':' . $primaryKey => $id,
        ]);

        return $statement->fetchObject($this->entityClass);
    }
}
