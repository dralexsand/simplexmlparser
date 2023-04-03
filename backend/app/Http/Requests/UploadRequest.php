<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !(Auth::id() === null);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required',
            'fileExtension' => 'in:xlsx,xls',
        ];
    }

    public function message()
    {
        return [
            'file.required' => 'The excel file required',
            'fileExtension.in' => 'The excel file must be a file of type: xls, xlsx'
        ];
    }

}
