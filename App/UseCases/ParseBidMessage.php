<?php

namespace App\UseCases;

class ParseBidMessage implements UseCase
{
    /** @var string */
    protected string $message;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function __invoke(): array
    {
        // BUD Ferrari 100
        list($prefix, $itemKey, $bidValue) = explode(' ', trim($this->message), 3);

        return [
            'item_key' => $itemKey,
            'amount' => intval(trim($bidValue)),
        ];
    }
}
