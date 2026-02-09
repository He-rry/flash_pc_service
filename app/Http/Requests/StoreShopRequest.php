<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
{
    /**
     * User တွင် ဆိုင်အသစ်ဖန်တီးပိုင်ခွင့်ရှိမရှိ စစ်ဆေးခြင်း
     */
    public function authorize(): bool
    {
        // User ရှိရမည်ဖြစ်ပြီး 'shop-create' သို့မဟုတ် 'manage-shops' permission ရှိရမည်
        return $this->user()?->can('shop-create') || $this->user()?->can('manage-shops');
    }

    /**
     * Validation Rules များသတ်မှတ်ခြင်း
     */
    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255|unique:shops,name',
            'lat'        => 'required|numeric',
            'lng'        => 'required|numeric',
            'address'    => 'nullable|string',
            'region'     => 'required|string|max:255',
            'created_at' => 'nullable|date',
        ];
    }

    /**
     * Error Messages များကို မြန်မာလို ပြသခြင်း
     */
    public function messages(): array
    {
        return [
            'name.required'   => 'ဆိုင်နာမည် ထည့်ပေးရန် လိုအပ်ပါသည်။',
            'name.unique'     => 'ဤဆိုင်နာမည်သည် ရှိပြီးသား ဖြစ်နေပါသည်။',
            'lat.required'    => 'မြေပုံညွှန်း (Latitude) လိုအပ်ပါသည်။',
            'lng.required'    => 'မြေပုံညွှန်း (Longitude) လိုအပ်ပါသည်။',
            'region.required' => 'တိုင်းဒေသကြီး/ပြည်နယ် ရွေးချယ်ပေးပါ။',
        ];
    }
}