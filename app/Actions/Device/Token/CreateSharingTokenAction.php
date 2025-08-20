<?php

namespace App\Actions\Device\Token;

use App\Actions\Validator\DeviceValidator;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use App\Models\DeviceSharingToken;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class CreateSharingTokenAction
{
    public function __construct(
        private readonly User $user,
        private readonly Device $device
    ) {}

    public function execute(): DeviceSharingToken
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $token = $this->createSharingToken();
                $this->logSuccess($this->device);

                return $token;
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    private function validateAttributesBeforeAction(): void
    {
        DeviceValidator::for($this->device)
            ->mustBeOwner($this->user)
            ->statusMustBeValidated();
    }

    /**
     * Create a new device sharing token.
     */
    private function createSharingToken(): DeviceSharingToken
    {
        $this->device->sharingToken()->delete();

        return $this->device->sharingToken()->create([
            'token' => $this->generateUniqueToken(),
            'expires_at' => now()->addDay(),
        ]);
    }

    /**
     * Generates a unique, random token that doesn't exist in the database yet.
     */
    private function generateUniqueToken(int $depth = 0): string
    {
        throw_if($depth > 8, new RuntimeException(
            'Maximum depth reached in random token generation.'
        ));

        $token = strtoupper(bin2hex(random_bytes(4)));
        $isUnique = DeviceSharingToken::where('token', $token)->doesntExist();

        return $isUnique ? $token : $this->generateUniqueToken($depth + 1);
    }

    /**
     * Log a success message in the event of a successful device sharing token creation.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->device->user->name} successfully created a device sharing token.", [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
        ]);
    }

    /**
     * Handles an exception that occurred during the action execution.
     */
    private function handleException(Exception $e): never
    {
        $this->logError($e);
        $this->throwException();
    }

    /**
     * Logs an error message when a device sharing token creation attempt fails.
     */
    private function logError(Exception $e): void
    {
        Log::error("The user {$this->device->user->name} failed to create a device sharing token.", [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'context' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * Throws an exception when a device sharing token creation attempt fails.
     */
    private function throwException(): never
    {
        throw new HttpJsonResponseException(
            trans('actions.device.errors.token'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
