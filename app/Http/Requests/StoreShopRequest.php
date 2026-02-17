<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('shop-create') || $this->user()?->can('manage-shops');
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255|unique:shops,name',
            'lat'        => 'required|numeric|between:-90,90', 
            'lng'        => 'required|numeric|between:-180,180',
            'address'    => 'nullable|string',
            'region'     => 'required|string|max:255',
            'created_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'ဆိုင်နာမည် ထည့်ပေးရန် လိုအပ်ပါသည်။',
            'name.unique'     => 'ဤဆိုင်နာမည်သည် ရှိပြီးသား ဖြစ်နေပါသည်။',
            'lat.required'    => 'မြေပုံညွှန်း (Latitude) လိုအပ်ပါသည်။',
            'lat.numeric'     => 'မြေပုံညွှန်းသည် ဂဏန်းဖြစ်ရပါမည်။',
            'lat.between'     => 'မှားယွင်းသော Latitude ဖြစ်နေပါသည်။ (-90 မှ 90 အတွင်းသာ ဖြစ်ရပါမည်)',
            'lng.required'    => 'မြေပုံညွှန်း (Longitude) လိုအပ်ပါသည်။',
            'lng.numeric'     => 'မြေပုံညွှန်းသည် ဂဏန်းဖြစ်ရပါမည်။',
            'lng.between'     => 'မှားယွင်းသော Longitude ဖြစ်နေပါသည်။ (-180 မှ 180 အတွင်းသာ ဖြစ်ရပါမည်)',
            'region.required' => 'တိုင်းဒေသကြီး/ပြည်နယ် ရွေးချယ်ပေးပါ။',
            'created_at.date' => 'ရက်စွဲ ပုံစံ မှားယွင်းနေပါသည်။',
        ];
    }
}