<?php

namespace App\Repositories;

use App\Entities\Entity;
use App\Entities\Item;
use PDO;

class ItemRepository extends MySqlRepository
{
    protected string $table = 'items';

    protected string $entityClass = Item::class;

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

        $statement = $this->getPdo()->prepare("SELECT `{$this->table}`.*, MAX(`bids`.`amount`) AS `current_bid` FROM `{$this->table}` LEFT OUTER JOIN `bids` ON `{$this->table}`.`id` = `bids`.`item_id`" . ($constraintsString ? " WHERE {$constraintsString}" : '') . " GROUP BY `items`.`id` LIMIT :limit OFFSET :offset");
        $statement->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, $this->entityClass);
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

        $statement = $this->getPdo()->prepare("SELECT `{$this->table}`.*, MAX(`bids`.`amount`) AS `current_bid` FROM `{$this->table}`, `bids` WHERE {$primaryKeyColumn} = :{$primaryKey}" . ($constraintsString ? " AND {$constraintsString}" : '') . " GROUP BY `{$this->table}`.`id`");
        $statement->execute([
            ':' . $primaryKey => $id,
        ]);

        return $statement->fetchObject($this->entityClass);
    }

    /**
     * @param string $key
     * @return Entity|null
     */
    public function findByKey(string $key): ?Entity
    {
        $constraintsString = $this->constraintsToSql();

        $statement = $this->getPdo()->prepare("SELECT `{$this->table}`.*, MAX(`bids`.`amount`) AS `current_bid` FROM `{$this->table}`, `bids` WHERE `key` = :key" . ($constraintsString ? " AND {$constraintsString}" : '') . " GROUP BY `{$this->table}`.`id`");
        $statement->execute([
            ':key' => $key,
        ]);

        return $statement->fetchObject($this->entityClass);
    }
}
