<?php

namespace App\Jobs\Device;

use App\Actions\DeviceInvoice\Validation\Validate\DeviceValidationAction;
use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ValidateDeviceRegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Device $device) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new DeviceValidationAction($this->device))->execute();
    }
}
