<?php

namespace App\Http\Controllers;

use App\Http\Resources\SingleDoctorConsultingReservationResource;
use App\Http\Resources\SingleDoctorResource;
use App\Models\Doctor;
use App\Models\DoctorConsultingReservation;
use App\Models\GeneralNotification;
use App\Models\Specification;
use App\Traits\GlobalTrait;
use App\Traits\SearchTrait;
use http\Env\Response;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PHPUnit\Framework\Constraint\Count;
use Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GlobalConsultingController extends Controller
{
    use GlobalTrait, SearchTrait;

    public function getConsultingCategories(Request $request)
    {
        try {

            $result = Specification::whereHas('doctors', function ($q) {
                $q->where('doctor_type', 'consultative');
            })->get(['id', DB::raw('name_' . $this->getCurrentLang() . ' as name')]);
            return $this->returnData('specifications', $result);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function getConsultingDoctorDetails(Request $request)
    {
        try {
            $requestData = $request->only(['doctor_id']);
            $doctor = Doctor::where('doctor_type', 'consultative')->find($requestData['doctor_id']);

            $result = new SingleDoctorResource($doctor);
            return $this->returnData('doctor', $result);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function getConsultingDoctorTimes(Request $request)
    {
        try {
            $requestData = $request->only(['doctor_id', 'reserve_duration', 'day_date']);
            $doctor = Doctor::where('doctor_type', 'consultative')->find($requestData['doctor_id']);
            $dayName = Str::lower(date('D', strtotime($requestData['day_date'])));

            if ($doctor) {

                $doctorTimes = [];

                if (count($doctor->consultativeTimes) > 0)
                    $times = $doctor->consultativeTimes()->where('day_code', $dayName)->get();
                else
                    $times = $doctor->times()->where('day_code', $dayName)->get();

                if ($times) {
                    foreach ($times as $key => $value) {
                        $splitTimes = $this->splitTimes($value->from_time, $value->to_time, $requestData['reserve_duration']);
                        foreach ($splitTimes as $k => $v) {
                            $s = [];
                            $s['id'] = $value->id;
                            $s['day_name'] = $value->day_name;
                            $s['day_code'] = $value->day_code;
                            $s['from_time'] = $v['from'];
                            $s['to_time'] = $v['to'];
                            $s['reservation_period'] = $value['reservation_period'];

                            array_push($doctorTimes, $s);
                        }

                    }
                }

            }

            return $this->returnData('times', $doctorTimes);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function reserveConsultingDoctor(Request $request)
    {
        try {
            $requestData = $request->all();
            $rules = [
                "doctor_id" => "required|numeric",
                "day_date" => "required|date",
                "from_time" => "required",
                "to_time" => "required",
                "price" => "required",
                "total_price" => "required",
                "hours_duration" => "required",
            ];
            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $doctor = Doctor::find($requestData['doctor_id']);
            $user = $this->auth('user-api');
            if ($user == null)
                return $this->returnError('E001', trans('messages.There is no user with this id'));

            $reservationCode = $this->getRandomString(8);
            $reservation = DoctorConsultingReservation::create([
                "reservation_no" => $reservationCode,
                "user_id" => $user->id,
                "doctor_id" => $doctor->id,
                "day_date" => date('Y-m-d', strtotime($requestData['day_date'])),
                "from_time" => date('H:i:s', strtotime($requestData['from_time'])),
                "to_time" => date('H:i:s', strtotime($requestData['to_time'])),
                "paid" => 0,
                'price' => (!empty($requestData['price']) ? $requestData['price'] : $doctor->price),
                'total_price' => empty($request->total_price) ? null : $request->total_price,
                "payment_method_id" => $request->payment_method_id,
                "hours_duration" => empty($request->hours_duration) ? null : $request->hours_duration,
            ]);

            if ($reservation) {

//                try {
//
//                    $reserve = new \stdClass();
//                    $reserve->reservation_no = $reservation->reservation_no;
//                    $reserve->day_date = date('l', strtotime($requestData['day_date']));
//                    $reserve->code = $reservation->code;
//                    $reserve->reservation_date = date('Y-m-d', strtotime($requestData['day_date']));
//                    $reserve->price = $reservation->price;
//                    $reserve->from_time = $reservation->from_time;
//                    $reserve->to_time = $reservation->to_time;
//
//                    $branch = ServiceReservation::find($reservation->id)->branch_id;
//                    $reserve->provider = Provider::providerSelection()->find($reservation->provider->provider_id);
//                    $reserve->branch = $branch;
//
//                    //push notification
//                    (new \App\Http\Controllers\NotificationController(['title' => __('messages.New Reservation'), 'body' => __('messages.You have new reservation')]))->sendProvider(Provider::find($service->provider_id)); // branch
//                    (new \App\Http\Controllers\NotificationController(['title' => __('messages.New Reservation'), 'body' => __('messages.You have new reservation')]))->sendProvider(Provider::find($service->provider_id)->provider); // main  provider
//
//                    $providerName = Provider::find($service->provider_id)->provider->{'name_' . app()->getLocale()};
//                    $smsMessage = __('messages.dear_service_provider') . ' ( ' . $providerName . ' ) ' . __('messages.provider_have_new_reservation_from_MedicalCall');
//                    $this->sendSMS(Provider::find($service->provider_id)->provider->mobile, $smsMessage);  //sms for main provider
//
//                    (new \App\Http\Controllers\NotificationController(['title' => __('messages.New Reservation'), 'body' => __('messages.You have new reservation')]))->sendProviderWeb(Provider::find($service->provider_id), null, 'new_reservation'); //branch
//                    (new \App\Http\Controllers\NotificationController(['title' => __('messages.New Reservation'), 'body' => __('messages.You have new reservation')]))->sendProviderWeb(Provider::find($service->provider_id)->provider, null, 'new_reservation');  //main provider
//                    $notification = GeneralNotification::create([
//                        'title_ar' => 'حجز جديد لدي مقدم الخدمة ' . ' ' . $providerName,
//                        'title_en' => 'New reservation for ' . ' ' . $providerName,
//                        'content_ar' => 'هناك حجز جديد برقم ' . ' ' . $reservation->reservation_no . ' ' . ' ( ' . $providerName . ' )',
//                        'content_en' => __('messages.You have new reservation no:') . ' ' . $reservation->reservation_no . ' ' . ' ( ' . $providerName . ' )',
//                        'notificationable_type' => 'App\Models\Provider',
//                        'notificationable_id' => $reservation->provider_id,
//                        'data_id' => $reservation->id,
//                        'type' => 1 //new reservation
//                    ]);
//                    $notify = [
//                        'provider_name' => $providerName,
//                        'reservation_no' => $reservation->reservation_no,
//                        'reservation_id' => $reservation->id,
//                        'content' => __('messages.You have new reservation no:') . ' ' . $reservation->reservation_no . ' ' . ' ( ' . $providerName . ' )',
//                        'photo' => $reserve->provider->logo,
//                        'notification_id' => $notification->id
//                    ];
//                    //fire pusher  notification for admin  stop pusher for now
//                    try {
//                        event(new \App\Events\NewReservation($notify));   // fire pusher new reservation  event notification*/
//                    } catch (\Exception $ex) {
//                    }
//                } catch (\Exception $ex) {
//                }

                $res = DoctorConsultingReservation::with(['doctor', 'paymentMethod'])->find($reservation->id);
                $result = new SingleDoctorConsultingReservationResource($res);
                return $this->returnData('reservation', $result);
            }

            return $this->returnError('E001', trans('main.oops_error'));

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    ##########################################################################

    public function getCurrentLang()
    {
        return app()->getLocale();
    }

    public function splitTimes($StartTime, $EndTime, $Duration = "30")
    {
        $returnArray = [];// Define output
        $StartTime = strtotime($StartTime); //Get Timestamp
        $EndTime = strtotime($EndTime); //Get Timestamp

        $addMinutes = $Duration * 60;

        for ($i = 0; $StartTime <= $EndTime; $i++) //Run loop
        {
            $from = date("G:i", $StartTime);
            $StartTime += $addMinutes; //End time check
            $to = date("G:i", $StartTime);
            if ($EndTime >= $StartTime) {
                $returnArray[$i]['from'] = $from;
                $returnArray[$i]['to'] = $to;
            }
        }
        return $returnArray;
    }

    protected function getRandomString($length)
    {
        $characters = '0123456789';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        $chkCode = DoctorConsultingReservation::where('reservation_no', $string)->first();
        if ($chkCode) {
            $this->getRandomString(8);
        }
        return $string;
    }


}