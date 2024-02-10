<?php

namespace App\Repositories;

use App\Entities\Bid;

class BidRepository extends MySqlRepository
{
    protected string $table = 'bids';

    protected string $entityClass = Bid::class;
}
