<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ContractRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        if (request('assigned_company') === null && request('assigned_store') === null && request('assigned_department') === null) {
            return [
                'name' => 'required',
                'start_date' => 'required',
                'end_date'  => 'required|after:start_date',
                'billing_date'  => 'required',
                'assigned_company' => 'required'
            ];      
        } else {
            return [
                'name' => 'required',
                //'name' => ['required', 'unique:servers,ip,'.$this->id.','.$request->input('id').',id,hostname,'.$request->input('hostname')],
                'start_date' => 'required',
                'end_date'  => 'required|after:start_date',
                'billing_date'  => 'required',
                'assigned_company' => 'unique:contracts,name'
            ];  
        }  
    }

    public function messages()
    {
        return [
            'assigned_company.required' => 'The company field is required.',
            'assigned_store.required' => 'The store field is required.',
            'assigned_department.required' => 'The department field is required.'
        ];
    }

    public function response(array $errors)
    {
        return $this->redirector->back()->withInput()->withErrors($errors, $this->errorBag);
    }
}
