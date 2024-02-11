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
     * @return Entity[]
     */
    public function page(int $page, int $perPage): array
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
     * @return Entity
     */
    public function find(int $id): Entity
    {
        $primaryKey = (new $this->entityClass())->getPrimaryKeyName();
        $primaryKeyColumn = snake_case($primaryKey);

        $constraintsString = $this->constraintsToSql();

        $statement = $this->getPdo()->prepare("SELECT * FROM `{$this->table}`, `bids` WHERE {$primaryKeyColumn} = :{$primaryKey}" . ($constraintsString ? " AND {$constraintsString}" : ''));
        $statement->execute([
            ':' . $primaryKey => $id,
        ]);

        return $statement->fetchObject($this->entityClass);
    }
}
