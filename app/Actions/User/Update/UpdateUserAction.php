<?php

namespace App\Actions\User\Update;

use App\Dto\User\UpdateUserDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserAction
{
    public function __construct(
        private readonly User $user,
        private readonly UpdateUserDto $data
    ) {}

    public function execute(): User
    {
        try {
            return DB::transaction(function () {
                $this->updateUser();
                $this->logSuccess();

                return $this->user;
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    private function updateUser(): void
    {
        $this->user->update([
            'name' => $this->data->name,
            'email' => $this->data->email,
            'phone' => $this->data->phone,
        ]);
    }

    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} successfully updated your profile.", [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
        ]);
    }

    private function handleException(Exception $e): never
    {
        $this->logError($e);
        $this->throwException();
    }

    private function logError(Exception $e): void
    {
        Log::error("The user {$this->user->name} failed to update your profile.", [
            'user_id' => $this->user->id,
            'context' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.user.errors.update'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
