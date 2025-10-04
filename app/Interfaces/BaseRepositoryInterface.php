<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all();

    public function find(int|string $id): ?Model;

    public function create(array $data): Model;

    public function update(int|string $id, array $data): ?Model;

    public function delete(int|string $id): bool;
}
