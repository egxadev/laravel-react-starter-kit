<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'     => 'required',
                'email'    => 'required|unique:users',
                'password' => 'required|confirmed',
                'roles'    => 'required|array'
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $user = $this->route('user');

            return [
                'name'     => 'required',
                'email'    => 'required|unique:users,email,' . $user->id,
                'password' => 'nullable|confirmed',
                'roles'    => 'required|array'
            ];
        }
    }
}
