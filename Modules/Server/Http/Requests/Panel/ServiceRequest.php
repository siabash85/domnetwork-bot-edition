<?php

namespace Modules\Server\Http\Requests\Panel;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "server_id" => ['required'],
            "package_duration_id" => ['required'],
            "package_id" => ['required'],
            "status" => ['required'],
            "price" => ['required'],

        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
