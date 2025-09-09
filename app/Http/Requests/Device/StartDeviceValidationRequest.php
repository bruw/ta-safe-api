<?php

namespace App\Http\Requests\Device;

use App\Dto\Device\Invoice\DeviceInvoiceDto;
use App\Http\Requests\ApiFormRequest;

class StartDeviceValidationRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('accessAsOwner', $this->device);
    }

    /**
     * Validates fields and creates a DeviceInvoiceDto from the request data.
     */
    public function invoiceData(): DeviceInvoiceDto
    {
        return new DeviceInvoiceDto(
            cpf: $this->cpf,
            name: $this->name,
            products: $this->products
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cpf' => ['required', 'string', 'max:16'],
            'name' => ['required', 'string', 'max:255'],
            'products' => ['required', 'string', 'max:16000'],
        ];
    }
}
