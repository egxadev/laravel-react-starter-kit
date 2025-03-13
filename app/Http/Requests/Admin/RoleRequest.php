<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
                'name'          => 'required|unique:roles,name',
                'permissions'   => 'required',
            ];
        }
    
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $role = $this->route('role');

            return [
                'name'          => 'required|unique:roles,name,' . $role->id,
                'permissions'   => 'required',
            ];
        }
    }
}
