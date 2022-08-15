<?php


namespace Lazyou\Quick\Http\Requests;

use Lazyou\Quick\Models\QuickUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return permission();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $id = $request->get('id');

        $rules = [
            'name' => [
                'required',
                'between:1,20',
            ],
            'status' => [
                'required',
                Rule::in([QuickUser::STATUS_ENABLE, QuickUser::STATUS_DISABLE]),
            ],
            'email' => [
                'required',
                'email',
                'between:6,20',
                "unique:quick_user,email,{$id}",
            ],
            'password' => [
                'between:6,20',
            ],
        ];

        if (! $id) {
            $rules['password'][] = 'required';
        }

        return $rules;
    }
}
