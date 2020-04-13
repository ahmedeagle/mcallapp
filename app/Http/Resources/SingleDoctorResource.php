<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleDoctorResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'doctor_type' => $this->doctor_type,
            'name' => app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en,
            'information' => app()->getLocale() == 'ar' ? $this->information_ar : $this->information_en,
            'abbreviation' => app()->getLocale() == 'ar' ? $this->abbreviation_ar : $this->abbreviation_en,
            'nickname' => app()->getLocale() == 'ar' ? $this->nickname->name_ar : $this->nickname->name_en,
            'specification' => app()->getLocale() == 'ar' ? $this->specification->name_ar : $this->specification->name_en,
            'price' => $this->price,
            'rate' => $this->rate,
            'photo' => $this->photo,
        ];

        return $result;
    }

}