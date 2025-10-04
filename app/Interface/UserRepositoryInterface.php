<?php

namespace App\Interface;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
