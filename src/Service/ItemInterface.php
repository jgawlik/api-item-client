<?php

declare(strict_types=1);

namespace ApiClient\Service;

interface ItemInterface
{
    public function getByParams(array $params): array;

    public function add(array $postParams): array;

    public function update(array $postParams): array;

    public function remove(int $itemId): array;
}