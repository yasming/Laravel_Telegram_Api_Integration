<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateTokenRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function all($keys = null)
    {
        $data = parent::all();
        $data['token'] = $this->route('token');

        return $data;
    }

    public function rules()
    {
        return [
            'token' => ['required', Rule::in(config('telegram.token'))]
        ];
    }
}
