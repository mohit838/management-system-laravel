<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    protected BaseRepository $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function find(int|string $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(int|string $id, array $data): ?Model
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }
}
