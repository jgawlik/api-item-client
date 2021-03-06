<?php

declare(strict_types=1);

namespace ApiClient\Service;

interface ItemInterface
{
    public function get(int $itemId): array;

    public function getByParams(array $params): array;

    public function add(array $postParams): array;

    public function update(array $patchParams, int $itemId): void;

    public function remove(int $itemId): void;
}
