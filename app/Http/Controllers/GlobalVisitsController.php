<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceReservationDetailsResource;
use App\Http\Resources\SingleServiceReservationResource;
use App\Models\CommentReport;
use App\Models\GeneralNotification;
use App\Models\Provider;
use App\Models\ReportingType;
use App\Models\Reservation;
use App\Models\ServiceReservation;
use App\Models\Service;
use App\Traits\GlobalTrait;
use App\Traits\SearchTrait;
use App\Traits\SMSTrait;
use Carbon\Carbon;
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

class GlobalVisitsController extends Controller
{
    use GlobalTrait, SearchTrait, SMSTrait;

    public function getClinicServiceAvailableTimes(Request $request)
    {
        try {
            $requestData = $request->all();
            $dayName = Str::lower(date('D', strtotime($requestData['reserve_day'])));
            $service = Service::find($requestData['service_id']);
            $serviceTimes = [];

            if ($requestData['service_type'] == 2) { // clinic
                if ($service) {
                    $serviceTimes = $service->times()->whereNotNull('reservation_period')->where('day_code', $dayName)->get();
                }
            } else {
                if ($service) {
                    $times = $service->times()->whereNull('reservation_period')->where('day_code', $dayName)->get();
                    foreach ($times as $key => $value) {
                        $splitTimes = $this->splitTimes($value->from_time, $value->to_time, $requestData['reserve_duration']);
                        foreach ($splitTimes as $k => $v) {
                            $s = [];
                            $s['id'] = $value->id;
                            $s['day_name'] = $value->day_name;
                            $s['day_code'] = $value->day_code;
                            $s['from_time'] = $v['from'];
                            $s['to_time'] = $v['to'];
                            $s['branch_id'] = $value->branch_id;

                            array_push($serviceTimes, $s);
                        }

                    }
                }
            }

            if ($serviceTimes) {

                ########### Start To Get Times After The Current Time ############
                $collection = collect($serviceTimes);
                $dayDate = $requestData['reserve_day'];

                $filtered = $collection->filter(function ($value, $key) use ($dayDate) {

                    // Check if this time is reserved before or not
                    $checkTime = ServiceReservation::where('day_date', $dayDate)
                        ->where('from_time', $value['from_time'])
                        ->where('to_time', $value['to_time'])
                        ->first();

                    return strtotime($value['from_time']) >= strtotime(date('H:i:s')) && is_null($checkTime);
                });
                $serTimes = array_values($filtered->all());
                ########### End To Get Times After The Current Time ############

                return $this->returnData('times', $serTimes);
            }

            return $this->returnError('E001', trans('main.there_is_no_times_now'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function reserveHomeClinicService(Request $request)
    {
        try {
            $requestData = $request->all();
            $rules = [
                "service_type" => "required|in:1,2", // 1== home & 2 == clinic
                "service_id" => "required|numeric",
                "day_date" => "required|date",
                "from_time" => "required",
                "to_time" => "required",
                "price" => "required",
            ];
            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $service = Service::find($requestData['service_id']);
            $user = $this->auth('user-api');
            if ($user == null)
                return $this->returnError('E001', trans('messages.There is no user with this id'));

            $reservationCode = $this->getRandomString(8);
            $reservation = ServiceReservation::create([
                "reservation_no" => $reservationCode,
                "user_id" => $user->id,
                "service_id" => $service->id,
                "day_date" => date('Y-m-d', strtotime($requestData['day_date'])),
                "from_time" => date('H:i:s', strtotime($requestData['from_time'])),
                "to_time" => date('H:i:s', strtotime($requestData['to_time'])),
                "paid" => 0,
                "service_type" => $request->service_type,
                "provider_id" => $service->provider_id,
                "branch_id" => $service->branch_id,
                'price' => (!empty($request->price) ? $requestData['price'] : $service->price),
                'total_price' => empty($request->total_price) ? null : $request->total_price,
                "latitude" => $request->latitude,
                "longitude" => $request->longitude,
                "payment_method_id" => $request->payment_method_id,
                "hours_duration" => empty($request->hours_duration) ? null : $request->hours_duration,
            ]);

            if ($reservation) {
                try {

                    $reserve = new \stdClass();
                    $reserve->reservation_no = $reservation->reservation_no;
                    $reserve->day_date = date('l', strtotime($requestData['day_date']));
                    $reserve->code = $reservation->code;
                    $reserve->reservation_date = date('Y-m-d', strtotime($requestData['day_date']));
                    $reserve->price = $reservation->price;
                    $reserve->from_time = $reservation->from_time;
                    $reserve->to_time = $reservation->to_time;
                    $branch = ServiceReservation::find($reservation->id)->branch_id;

                    $reserve->provider = Provider::providerSelection()->find($reservation->provider->provider_id);
                    $reserve->branch = $branch;

                    //push notification
                    (new \App\Http\Controllers\NotificationController(['title' => __('messages.New Reservation'), 'body' => __('messages.You have new reservation')]))->sendProvider(Provider::find($service->provider_id)); // branch
                    (new \App\Http\Controllers\NotificationController(['title' => __('messages.New Reservation'), 'body' => __('messages.You have new reservation')]))->sendProvider(Provider::find($service->provider_id)->provider); // main  provider

                    $providerName = Provider::find($service->provider_id)->provider->{'name_' . app()->getLocale()};
                    $smsMessage = __('messages.dear_service_provider') . ' ( ' . $providerName . ' ) ' . __('messages.provider_have_new_reservation_from_MedicalCall');
                    $this->sendSMS(Provider::find($service->provider_id)->provider->mobile, $smsMessage);  //sms for main provider

                    (new \App\Http\Controllers\NotificationController(['title' => __('messages.New Reservation'), 'body' => __('messages.You have new reservation')]))->sendProviderWeb(Provider::find($service->provider_id), null, 'new_reservation'); //branch
                    (new \App\Http\Controllers\NotificationController(['title' => __('messages.New Reservation'), 'body' => __('messages.You have new reservation')]))->sendProviderWeb(Provider::find($service->provider_id)->provider, null, 'new_reservation');  //main provider
                    $notification = GeneralNotification::create([
                        'title_ar' => 'حجز خدمة جديد لدي مقدم الخدمة ' . ' ' . $providerName,
                        'title_en' => 'New service reservation for ' . ' ' . $providerName,
                        'content_ar' => 'هناك حجز خدمة جديد برقم ' . ' ' . $reservation->reservation_no . ' ' . ' ( ' . $providerName . ' )',
                        'content_en' => __('messages.You have new reservation no:') . ' ' . $reservation->reservation_no . ' ' . ' ( ' . $providerName . ' )',
                        'notificationable_type' => 'App\Models\Provider',
                        'notificationable_id' => $reservation->provider_id,
                        'data_id' => $reservation->id,
                        'type' => 6 // new service reservation
                    ]);
                    $notify = [
                        'provider_name' => $providerName,
                        'reservation_no' => $reservation->reservation_no,
                        'reservation_id' => $reservation->id,
                        'content' => __('messages.You have new reservation no:') . ' ' . $reservation->reservation_no . ' ' . ' ( ' . $providerName . ' )',
                        'photo' => $reserve->provider->logo,
                        'notification_id' => $notification->id
                    ];
                    //fire pusher  notification for admin  stop pusher for now
                    try {
                        event(new \App\Events\NewReservation($notify));   // fire pusher new reservation  event notification*/
                    } catch (\Exception $ex) {
                    }
                } catch (\Exception $ex) {
                }

                $res = ServiceReservation::with(['service', 'provider', 'branch', 'paymentMethod'])->find($reservation->id);
                $result = new SingleServiceReservationResource($res);
                return $this->returnData('reservation', $result);
            }
            return $this->returnError('E001', trans('main.oops_error'));

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function getAllServicesReservations(Request $request)
    {
        try {
            $user = $this->auth('user-api');
            $serviceReservations = ServiceReservation::with(['service', 'provider', 'branch', 'paymentMethod'])->where('user_id', $user->id)->paginate(10);
            if (count($serviceReservations->toArray()) > 0) {
                $total_count = $serviceReservations->total();
                $serviceReservations = json_decode($serviceReservations->toJson());
                $reservationsJson = new \stdClass();
                $reservationsJson->current_page = $serviceReservations->current_page;
                $reservationsJson->total_pages = $serviceReservations->last_page;
                $reservationsJson->total_count = $total_count;
                $reservationsJson->data = $serviceReservations->data;
            }
            return $this->returnData('reservations', $reservationsJson);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function getRejectedReasons(Request $request)
    {
        if (app()->getLocale() == 'ar') {
            $reasons = [
                ['id' => 1, 'name' => 'كنت أقوم بالتجربة فقط',],
                ['id' => 2, 'name' => 'حجز مكرر',],
                ['id' => 3, 'name' => 'حجزت بالخطأ',],
                ['id' => 4, 'name' => 'لم أعد أرغب بالموعد',]
            ];
        } else {
            $reasons = [
                ['id' => 1, 'name' => 'I was just experimenting',],
                ['id' => 2, 'name' => 'Duplicate reservation',],
                ['id' => 3, 'name' => 'Booked by mistake',],
                ['id' => 4, 'name' => 'I no longer want the appointment',]
            ];
        }

        return $this->returnData('reasons', $reasons);
    }

    public function rejectServiceReservation(Request $request)
    {
        try {
            $requestData = $request->all();
            $rules = [
                "reservation_id" => "required",
                "rejected_reason_id" => "required",
                "rejected_reason_notes" => "required",
            ];
            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $reservation = ServiceReservation::find($requestData['reservation_id']);

            if ($reservation) {
                $reservation->update([
                    'approved' => 2, // canceled
                    'rejected_reason_id' => $requestData['rejected_reason_id'],
                    'rejected_reason_notes' => $requestData['rejected_reason_notes'],
                ]);
            }

            return $this->returnData('reservation', $reservation);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function getServiceReservationDetails(Request $request)
    {
        try {
//            $user = $this->auth('user-api');
            $serviceReservations = ServiceReservation::with(['service', 'provider', 'branch', 'paymentMethod'])->find($request->id);
            $result = new ServiceReservationDetailsResource($serviceReservations);
            return $this->returnData('reservation', $result);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    ################################# Start rate provider in offer reservation ############################

    public function rateOfferReservation(Request $request)
    {
        try {
            $requestData = $request->all();
            $rules = [
                "id" => "required|numeric", // Reservation ID
                "provider_rate" => "required",
                "rate_comment" => "nullable",
                "bill_photo" => "sometimes|nullable",
            ];
            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $user = $this->auth('user-api');
            if ($user == null)
                return $this->returnError('E001', trans('messages.There is no user with this id'));

            $reservation = Reservation::find($requestData['id']);
            $provider = Provider::find($reservation->provider_id);
            $MainProvider = $provider->Provider;

            if ($MainProvider == null)
                return $this->returnError('E001', trans('messages.No provider with this id'));

            if ($reservation) {

                $bill_photo_path = isset($requestData['bill_photo']) && !empty($requestData['bill_photo']) ? $this->saveImage('bills', $requestData['bill_photo']) : null;
                $reservation->update([
                    "provider_rate" => $requestData['provider_rate'],
                    "rate_comment" => $requestData['rate_comment'],
                    'rate_date' => Carbon::now(),
                    "bill_photo" => $bill_photo_path,
                ]);

                $query = Reservation::where('provider_id', $reservation->provider_id);

                $providerReservationsCount = $query->count();
                $providerReservationsSum = $query->sum('provider_rate');

                if (floatval($providerReservationsCount) != 0) {
                    $providerRate = floatval($providerReservationsSum) / floatval($providerReservationsCount);
                } else {
                    $providerRate = 0;
                }

                $provider->update([
                    "rate" => $providerRate,
                ]);

                $notification = GeneralNotification::create([
                    'title_ar' => 'تقييم جديد لمقدم الخدمه  ' . ' ' . '(' . $MainProvider->name_ar . ')',
                    'title_en' => 'New rating for ' . ' ' . '(' . $MainProvider->name_ar . ')',
                    'content_ar' => ' تقييم  جديد علي الحجز بعرض رقم ' . ' ' . $reservation->reservation_no,
                    'content_en' => 'New rating for offer reservation No: ' . ' ' . $reservation->reservation_no . ' ' . ' ( ' . $MainProvider->name_ar . ' )',
                    'notificationable_type' => 'App\Models\Provider',
                    'notificationable_id' => $reservation->provider_id,
                    'data_id' => $reservation->id,
                    'type' => 5 //user rate provider and doctor
                ]);

                $notify = [
                    'provider_name' => $MainProvider->name_ar,
                    'reservation_no' => $reservation->reservation_no,
                    'reservation_id' => $reservation->id,
                    'content' => ' تقييم  جديد علي الحجز رقم ' . ' ' . $reservation->reservation_no,
                    'photo' => $MainProvider->logo,
                    'notification_id' => $notification->id
                ];
                //fire pusher  notification for admin  stop pusher for now
                try {
                    event(new \App\Events\NewProviderRate($notify));   // fire pusher new reservation  event notification*/
                } catch (\Exception $ex) {
                }

                return $this->returnSuccessMessage(trans('messages.Rate saved successfully'));

            }

            return $this->returnError('E001', trans('main.oops_error'));

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    ################################# End rate provider in offer reservation ############################

    ################################# Start report service rate ############################

    public function reportService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "reservation_no" => "required|max:191",
            "reporting_type_id" => "required",
            "report_comment" => "sometimes|nullable",
        ]);

        $user = $this->auth('user-api');
        if ($user == null)
            return $this->returnError('E001', trans("messages.User not found"));

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $reservation = ServiceReservation::where('reservation_no', $request->reservation_no)->first();
        if ($reservation == null)
            return $this->returnError('D000', trans('messages.No reservation with this number'));

        $reporting_type = ReportingType::find($request->reporting_type_id);
        if ($reporting_type == null)
            return $this->returnError('D000', trans('messages.No reporting type with this id'));

        CommentReport::create([
            'user_id' => $user->id,
            'reservation_no' => $request->reservation_no,
            'reporting_type_id' => $request->reporting_type_id,
            'report_comment' => $request->report_comment,
        ]);
        return $this->returnSuccessMessage(trans('messages.Comment Reported  successfully'));
    }

    ################################# End report service rate ############################


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
        $chkCode = ServiceReservation::where('reservation_no', $string)->first();
        if ($chkCode) {
            $this->getRandomString(8);
        }
        return $string;
    }

    public function get_checkout_id(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "price" => array('required', 'regex:/^\d+(\.\d{1,2})?$/', 'min:1'),
        ]);
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $user = $this->auth('user-api');
        $userEmail = $user->email ? $user->email : 'info@wisyst.info';

        $url = "https://test.oppwa.com/v1/checkouts";
        $data =
            "entityId=8ac7a4ca6d0680f7016d14c5bbb716d8" .
            "&amount=" . $request->price .
            "&currency=SAR" .
            "&paymentType=DB" .
            "&notificationUrl=" .
            "&merchantTransactionId=400" .
            "&testMode=EXTERNAL" .
            "&customer.email=" . $userEmail;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer OGFjN2E0Y2E2ZDA2ODBmNzAxNmQxNGM1NzMwYzE2ZDR8QVpZRXI1ZzZjZQ'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if (curl_errno($ch)) {

                // return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
            }
            curl_close($ch);

        } catch (\Exception $ex) {

            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

        $id = json_decode($responseData)->id;
        return $this->returnData('checkoutId', $id, trans('messages.Checkout id successefully retrieved'), 'S001');

    }

    public function checkPaymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "checkoutId" => 'required',
        ]);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $url = "https://test.oppwa.com/v1/checkouts/{$request -> checkoutId}/payment";
        $url .= "?entityId=8ac7a4ca6d0680f7016d14c5bbb716d8";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer OGFjN2E0Y2E2ZDA2ODBmNzAxNmQxNGM1NzMwYzE2ZDR8QVpZRXI1ZzZjZQ'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);

        }
        curl_close($ch);

        $r = json_decode($responseData);

        return $this->returnData('status', $r->result, trans('messages.Payment status'), 'S001');

    }

}
