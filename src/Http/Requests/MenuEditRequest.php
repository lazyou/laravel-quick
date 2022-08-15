<?php


namespace Lazyou\Quick\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class MenuEditRequest extends FormRequest
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
        $parentId = $request->get('parent_id');

        $rules = [
            'parent_id' => [
                'required',
            ],
            'name' => [
                'required',
                'between:1,20',
            ],
            'url' => [
                'required',
                'between:1,30',
                //                "unique:permission,url,{$id}",
            ],
            'sort' => [
                'required',
                'numeric',
            ],
        ];

        if ($parentId) {
            $rules['parent_id'][] = 'exists:permission,id';
        }

        return $rules;
    }
}
