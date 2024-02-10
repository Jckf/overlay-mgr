<?php

namespace App\UseCases;

interface UseCase
{
    /**
     * @return mixed
     */
    public function __invoke(): mixed;
}
