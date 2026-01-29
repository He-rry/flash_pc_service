<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Admin ဖြစ်မှ ခွင့်ပြုမယ်ဆိုတဲ့ logic မျိုး ဒီမှာ ထည့်နိုင်ပါတယ်
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255|unique:shops,name',
            'lat'     => 'required|numeric',
            'lng'     => 'required|numeric',
            'address' => 'nullable|string',
            'region'  => 'required|string|max:255',
            'created_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ဆိုင်နာမည် ထည့်ပေးရန် လိုအပ်ပါသည်။',
            'name.unique'   => 'ဤဆိုင်နာမည်သည် ရှိပြီးသား ဖြစ်နေပါသည်။',
            'lat.required'  => 'မြေပုံညွှန်း (Latitude) လိုအပ်ပါသည်။',
            'lng.required'  => 'မြေပုံညွှန်း (Longitude) လိုအပ်ပါသည်။',
            'region.required' => 'တိုင်းဒေသကြီး/ပြည်နယ် ရွေးချယ်ပေးပါ။',
        ];
    }
}
