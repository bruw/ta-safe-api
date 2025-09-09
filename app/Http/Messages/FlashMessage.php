<?php

namespace App\Http\Messages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashMessage extends JsonResource
{
    public const SUCCESS = 'success';
    public const INFO = 'info';
    public const WARNING = 'warning';
    public const ERROR = 'error';

    public function __construct(private string $type, private string $msg) {}

    /**
     * Merge data for the FlashMessage instance.
     */
    public function merge($data = [])
    {
        return $this->toArray() + $data;
    }

    /**
     * Convert the FlashMessage instance to an array.
     */
    public function toArray(?Request $request = null): array
    {
        return ['message' => ['type' => $this->type, 'text' => $this->msg]];
    }

    /**
     * Create a new FlashMessage instance with a success type.
     */
    public static function success(string $msg): FlashMessage
    {
        return new FlashMessage(type: self::SUCCESS, msg: $msg);
    }

    /**
     * Create a new FlashMessage instance with an info type.
     */
    public static function info(string $msg): FlashMessage
    {
        return new FlashMessage(type: self::INFO, msg: $msg);
    }

    /**
     * Create a new FlashMessage instance with a warning type.
     */
    public static function warning(string $msg): FlashMessage
    {
        return new FlashMessage(type: self::WARNING, msg: $msg);
    }

    /**
     * Create a new FlashMessage instance with an error type.
     */
    public static function error(string $msg): FlashMessage
    {
        return new FlashMessage(type: self::ERROR, msg: $msg);
    }
}
