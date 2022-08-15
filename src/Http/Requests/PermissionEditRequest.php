<?php


namespace Lazyou\Quick\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PermissionEditRequest extends FormRequest
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
        $id = $request->get('id', 0);

        return [
            'name' => [
                'nullable',
                'between:1,20',
            ],
            'as' => [
                'required',
            ],
        ];
    }
}
