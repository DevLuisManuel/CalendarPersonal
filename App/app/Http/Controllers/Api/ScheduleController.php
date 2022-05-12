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
            message: "List Calendar",
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
            message: "Successfully Assigned Dance Date.",
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
            message: "appointment success",
        ) : $this->response(
            data: [],
            message: "appointment False",
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
                message: "appointment Update",
            );
        }
        return $this->response(
            data: [],
            message: "appointment Not exists",
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
                message: "appointment Delete Successfull",
            );
        }

        return $this->response(
            data: [],
            message: "appointment Not exists",
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
            ] //Rules
        );
        //We start the validation
        if ($validator->fails()) {
            return [
                "data" => [],
                "errors" => $validator->messages()->toArray(),
                "message" => "Error de validacion",
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
                "message" => "This date cannot be scheduled, it's out range of work hours.",
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
                "message" => "This date cannot be scheduled, it is already being used.",
                "success" => false,
            ];
        }
        return ["success" => true];
    }
}
