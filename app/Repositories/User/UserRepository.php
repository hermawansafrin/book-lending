<?php

namespace App\Repositories\User;

class UserRepository
{
    /**
     * Create a new user.
     */
    public function create(array $input): array
    {
        $createdUserId = app(Creator::class)->create($input);

        return $this->findOne($createdUserId);
    }

    /**
     * Find one user by id
     */
    private function findOne(int $id): ?array
    {
        return app(Getter::class)->getById($id);
    }
}
