<?php

namespace App\Entities;

/**
 * @method int|null getId()
 * @method $this setId(int $id)
 * @method string|null getKey()
 * @method $this setKey(string $key)
 * @method string|null getTitle()
 * @method $this setTitle(string $title)
 * @method string|null getDescription()
 * @method $this setDescription(string $description)
 * @method string|null getImage()
 * @method $this setImage(string $image)
 * @method int|null getCurrentBid()
 * @method $this setCurrentBid(int $currentBid)
 */
class Item extends Entity
{
    /** @var int|null */
    protected ?int $id = null;

    /** @var string|null */
    protected ?string $key = null;

    /** @var string|null */
    protected ?string $title = null;

    /** @var string|null */
    protected ?string $description = null;

    /** @var string|null */
    protected ?string $image = null;

    /** @var int|null */
    protected ?int $currentBid = null;
}
