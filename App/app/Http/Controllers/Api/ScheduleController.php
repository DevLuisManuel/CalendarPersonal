<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AppointmentResource;
use App\Models\Calendar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\{JsonResponse, Request};

class ScheduleController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function listAppointments()
    {
        return $this->response(
            data: AppointmentResource::collection(Calendar::all())->jsonSerialize(),
            message: __('Schedule/Appointment.listAppointment'),
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createAppointment(Request $request)
    {
        //Call Function Validator
        $valid = $this->validations($request);
        if (!$valid['success']) {
            return $this->response(
                data: $valid['data'],
                message: $valid['message'],
                errors: $valid['errors'],
                success: $valid['success']
            );
        }

        if (!$request->get('exist')) {
            $person = new User();
            $person->setAttribute('name', $request->get('person')['name']);
            $person->setAttribute('email', $request->get('person')['email']);
            $person->save();
        } else {
            $person = User::query()->find($request->get('person')['id']);
        }

        $calendar = new Calendar();
        $calendar->setAttribute('appointmentDate', $request->get('appointmentDate'));
        $calendar->user()->associate($person);
        $calendar->save();

        return $this->response(
            data: (new AppointmentResource($calendar))->jsonSerialize(),
            message: __('Schedule/Appointment.appointmentAssigned'),
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function readAppointment(int $id)
    {
        $calendar = Calendar::query()->find($id);
        return ($calendar) ? $this->response(
            data: (new AppointmentResource($calendar))->jsonSerialize(),
            message: __('Schedule/Appointment.success.read'),
        ) : $this->response(
            data: [],
            message: __('Schedule/Appointment.errors.notExistappointment'),
            success: false
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateAppointment(Request $request, int $id)
    {
        $calendar = Calendar::query()->find($id);
        if ($calendar) {
            //Call Function Validator
            $valid = $this->validations($request);
            if (!$valid['success']) {
                return $this->response(
                    data: $valid['data'],
                    message: $valid['message'],
                    errors: $valid['errors'],
                    success: $valid['success']
                );
            }

            $calendar->setAttribute('appointmentDate', $request->get('appointmentDate'));
            $calendar->save();
            return $this->response(
                data: (new AppointmentResource($calendar))->jsonSerialize(),
                message: __('Schedule/Appointment.success.update'),
            );
        }
        return $this->response(
            data: [],
            message: __('Schedule/Appointment.errors.notExistappointment'),
            success: false
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function deleteAppointment($id)
    {
        $calendar = Calendar::query()->find($id);
        if ($calendar) {
            $calendar->delete();
            return $this->response(
                data: [],
                message: __('Schedule/Appointment.success.delete'),
            );
        }
        return $this->response(
            data: [],
            message: __('Schedule/Appointment.errors.notExistappointment'),
            success: false
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    private function validations(Request $request): array
    {
        $validator = Validator::make($request->all(),
            [
                "appointmentDate" => "required|date_format:Y-m-d H:i|after_or_equal:" . Carbon::now()->format('Y-m-d H:i'),
                "exist" => "required|boolean",
                "person" => "array|array",
                "person.id" => "required_if:exist,true",
                "person.name" => "required_if:exist,false",
                "person.email" => "email|required_if:exist,false",
            ], //Rules
            [],
            __('Schedule/Attributes.attributes')
        );
        //We start the validation
        if ($validator->fails()) {
            return [
                "data" => [],
                "errors" => $validator->messages()->toArray(),
                "message" => __('Schedule/Appointment.errors.validation'),
                "success" => false
            ];
        }
        $checkHoursWork = (new Carbon($request->get('appointmentDate')))
            ->between(
                Carbon::createFromFormat('H:i a', config('Work.hours')[0]),
                Carbon::createFromFormat('H:i a', config('Work.hours')[1])
            );
        //Validate Work Hour
        if (!(new Collection(config('Work.days')))->contains((new Carbon($request->get('appointmentDate')))->format('l'))
            && !$checkHoursWork) {
            return [
                "data" => [],
                "errors" => [],
                "message" => __('Schedule/Appointment.errors.workDay'),
                "success" => false,
            ];
        }

        $finishDate = (new Carbon($request->get('appointmentDate')))->addHours();
        $calendar = Calendar::query()
            ->whereBetween("appointmentDate",
                [
                    (new Carbon($request->get('appointmentDate')))->format('Y-m-d H:i:00'),
                    $finishDate->format('Y-m-d H:i:00'),
                ])
            ->get();
        if ($calendar->count() > 0) {
            return [
                "data" => [],
                "errors" => [],
                "message" => __('Schedule/Appointment.errors.appointmentDate'),
                "success" => false,
            ];
        }
        return ["success" => true];
    }
}
