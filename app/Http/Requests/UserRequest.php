<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username'=>'nullable|string|unique:users',
            'password'=>'string|nullable',
            'full_name'=>'string|nullable',
            'roles'=>'array|nullable',
        ];
    }

    public function prepareForValidation()
    {
        $re = [];
        if(!empty($this->username)){
            $re['username'] = $this->username;
        }
        if(!empty($this->full_name)){
            $re['full_name'] = $this->full_name;
        }
        if(!empty($this->password)){
            $re['password'] = bcrypt($this->password);
        }
        $this->merge($re);
    }
}
