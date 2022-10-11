<?php

namespace App\Http\Requests\Car;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string fabricator
 * @property string model
 * @property int user_id
 */
class UpdateCar extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id'    => 'required|integer',
            'fabricator' => 'required_with_all:name|string',
            'model'      => 'required_with_all:fabricator|string'
        ];
    }
}
