<?php

namespace App\Entities;

/**
 * @method int getId()
 * @method $this setId(int $id)
 * @method string getRecipient()
 * @method $this setRecipient(string $recipient)
 * @method string getSender()
 * @method $this setSender(string $sender)
 * @method string getOriginalMessage()
 * @method $this setOriginalMessage(string $originalMessage)
 * @method int getItemId()
 * @method $this setItemId(int $itemId)
 * @method int getAmount()
 * @method $this setAmount(int $amount)
 * @method int getTimestamp()
 * @method $this setTimestamp(int $timestamp)
 */
class Bid extends Entity
{
    /** @var int|null */
    protected ?int $id = null;

    /** @var string|null */
    protected ?string $recipient = null;

    /** @var string|null */
    protected ?string $sender = null;

    /** @var string|null */
    protected ?string $originalMessage = null;

    /** @var int|null */
    protected ?int $itemId = null;

    /** @var int|null */
    protected ?int $amount = null;

    /** @var int|null */
    protected ?int $timestamp = null;

    public function jsonSerialize(): array
    {
        $len = strlen($this->getSender());
        $asterisks = $len - 3;

        return [
            'id' => $this->getId(),
            'sender' => str_repeat('*', $asterisks) . substr($this->getSender(), -3),
            'itemId' => $this->getItemId(),
            'amount' => $this->getAmount(),
            'timestamp' => $this->getTimestamp(),
        ];
    }
}
