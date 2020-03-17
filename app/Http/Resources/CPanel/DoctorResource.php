<?php

namespace App\Http\Resources\CPanel;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DoctorResource extends ResourceCollection
{
    public function toArray($request)
    {
        $result['data'] = $this->collection->transform(function ($data) {
            return [
                'id' => $data->id,
                'name' => app()->getLocale() == 'ar' ? $data->name_ar : $data->name_en,
                'gender' => $data->gender == 1 ? __('main.male') : __('main.female'),
                'nickname' => app()->getLocale() == 'ar' ? $data->nickname->name_ar : $data->nickname->name_en,
                'specification' => app()->getLocale() == 'ar' ? $data->specification->name_ar : $data->specification->name_en,
                'nationality' => app()->getLocale() == 'ar' ? $data->nationality->name_ar : $data->nationality->name_en,
                'provider' => app()->getLocale() == 'ar' ? $data->provider->name_ar : $data->provider->name_en,
                'price' => $data->price,
                'status' => $data->status == '1' ? __('main.active') : __('main.not_active'),
            ];
        });

        $result['pagination'] = [
            'total' => $this->total(),
            'count' => $this->count(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage()
        ];
        return $result;
    }

}
