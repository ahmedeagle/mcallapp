<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleServiceReservationResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'reservation_no' => $this->reservation_no,
            'day_date' => $this->day_date,
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
            'status' => $this->status,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'service' => [
                'id' => $this->service->id,
                'title' => app()->getLocale() == 'ar' ? $this->service->title_ar : $this->service->title_en,
                'price' => $this->service->price,
            ],
            'provider' => [
                'id' => $this->provider->id,
                'name' => app()->getLocale() == 'ar' ? $this->provider->name_ar : $this->provider->name_en,
            ],
            'branch' => [
                'id' => $this->branch->id,
                'name' => app()->getLocale() == 'ar' ? $this->branch->name_ar : $this->branch->name_en,
                'parent_type' => $this->branch->parent_type->name,
            ],
//            'payment_method' => [
//                'id' => $this->payment_method->id,
//                'name' => app()->getLocale() == 'ar' ? $this->payment_method->name_ar : $this->payment_method->name_en,
//            ],
        ];

        return $result;
    }

}
