<?php

namespace App\Repositories;

use App\Database\MySqlDatabase;
use App\Entities\Entity;
use PDO;

class MySqlRepository implements Repository
{
    /** @var MySqlDatabase */
    protected MySqlDatabase $database;

    /** @var string */
    protected string $table;

    /** @var class-string<Entity> */
    protected string $entityClass;

    /** @var array */
    protected array $constraints = [];

    /**
     * @param MySqlDatabase $database
     */
    public function __construct(MySqlDatabase $database)
    {
        $this->database = $database;
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

        $statement = $this->database->prepare("INSERT INTO {$this->table} ({$columnsString}) VALUES ({$placeholdersString})");

        if (!$statement->execute($values)) {
            return false;
        }

        $entity->setAttribute($entity->getPrimaryKeyName(), $this->database->lastInsertId());

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

        $statement = $this->database->prepare("UPDATE {$this->table} SET {$columnsString} WHERE {$primaryKeyColumn} = :{$primaryKey}");

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

        $constraintsString = $this->constraintsToSql();

        $statement = $this->database->prepare("DELETE FROM {$this->table} WHERE {$primaryKeyColumn} = :{$primaryKey}" . ($constraintsString ? " AND {$constraintsString}" : ''));

        return $statement->execute([
            ':' . $primaryKey => $entity->getAttribute($primaryKey),
        ]);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param string $orderBy
     * @param string $direction
     * @return Entity[]
     */
    public function page(int $page, int $perPage, string $orderBy = 'id', string $direction = 'desc'): array
    {
        $offset = ($page - 1) * $perPage;

        $constraintsString = $this->constraintsToSql();

        $statement = $this->database->prepare("SELECT * FROM {$this->table}" . ($constraintsString ? " WHERE {$constraintsString}" : '') . " ORDER BY `{$orderBy}` {$direction} LIMIT :limit OFFSET :offset");
        $statement->execute([
            ':limit' => $perPage,
            ':offset' => $offset,
        ]);

        return $statement->fetchAll($this->entityClass);
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function find(int $id): ?Entity
    {
        $primaryKey = (new $this->entityClass())->getPrimaryKeyName();
        $primaryKeyColumn = snake_case($primaryKey);

        $constraintsString = $this->constraintsToSql();

        $statement = $this->database->prepare("SELECT * FROM {$this->table} WHERE {$primaryKeyColumn} = :{$primaryKey}" . ($constraintsString ? " AND {$constraintsString}" : ''));
        $statement->execute([
            ':' . $primaryKey => $id,
        ]);

        return $statement->fetch($this->entityClass);
    }

    /**
     * @param string $attribute
     * @param string $operator
     * @param mixed $value
     * @deprecated Constraints are vulnerable to SQL injection attacks.
     */
    public function constrain(string $attribute, string $operator, mixed $value): int
    {
        $id = count($this->constraints) - 1;

        $this->constraints[$id] = [
            'attribute' => $attribute,
            'operator' => $operator,
            'value' => $value,
        ];

        return $id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function removeConstraint(int $id): void
    {
        unset($this->constraints[$id]);
    }

    /**
     * @return string
     */
    protected function constraintsToSql(): string
    {
        $sql = [];

        foreach ($this->constraints as $constraint) {
            $sql[] = sprintf(
                '%s %s %s',
                snake_case($constraint['attribute']),
                $constraint['operator'],
                $constraint['value'],
            );
        }

        return implode(' AND ', $sql);
    }
}
