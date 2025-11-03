<?php

namespace App\Features\Projects\Requests;

use App\Http\Requests\AbstractRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreProjectRequest extends AbstractRequest {
/*     
Get the validation rules that apply to the request.*
@return array<string, ValidationRule|array|string>*/
public function rules(): array {
    return ['owner_id' => ['integer', 'exists:users,id'],'title' => ['required', 'string'],'description' => ['string'],'has_static_task_attributes' => ['boolean'],'category_id' => ['nullable', 'integer'],];}

    protected function prepareForValidation(): void {
        $this->merge([
            'owner_id' => auth()->id(),
            'has_static_task_attributes' => $this->input('hasStaticTaskAttributes') ?? false,
            'category_id' => $this->input('categoryId'),
        ]);
    }
}