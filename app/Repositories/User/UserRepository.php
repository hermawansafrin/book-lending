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
     * Get users with pagination.
     */
    public function pagination(array $input): array
    {
        return app(Getter::class)->getPagination($input);
    }

    /**
     * Make user as admin.
     */
    public function makeUserAsAdmin(int $userId): array
    {
        app(Updater::class)->makeUserAsAdmin($userId);

        return $this->findOne($userId);
    }

    /**
     * Find one user by id
     */
    private function findOne(int $id): ?array
    {
        return app(Getter::class)->getById($id);
    }

    /**
     * Check if user is admin.
     */
    public function isUserAsAdmin(int $userId): bool
    {
        return app(Getter::class)->isUserAsAdmin($userId);
    }
}
