<?php

namespace App\Http\Requests;

class ReplyRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content'=>'required|min:3'
        ];
    }

}
