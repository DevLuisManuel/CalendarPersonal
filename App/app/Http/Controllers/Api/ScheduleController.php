<?php

namespace App\Http\Controllers\Api;

use App\Http\{Controllers\BaseController, Resources\AppointmentResource};
use App\Models\{Calendar, User};
use Carbon\Carbon;
use Illuminate\Support\{Collection, Facades\Validator};
use Illuminate\Http\{JsonResponse, Request};
/**
 * @OA\Tag(
 *     name="Schedule",
 *     description="API Endpoints of Schedule"
 * )
 */
class ScheduleController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/projects",
     *     @OA\Response(response="200", description="Display a listing of projects.")
     * )
     */
    public function listAppointments(): JsonResponse
    {
        return $this->response(
            data: AppointmentResource::collection(Calendar::query()->orderBy('appointmentDate')->get())->jsonSerialize(),
            message: __('Schedule/Appointment.listAppointment'),
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createAppointment(Request $request): JsonResponse
    {
        //Call Function Validator
        $valid = $this->validations($request);
        if (!$valid['Success']) {
            return $this->response(
                data: $valid['Data'],
                message: $valid['Message'],
                errors: $valid['Errors'],
                success: $valid['Success']
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
    public function readAppointment(int $id): JsonResponse
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
    public function updateAppointment(Request $request, int $id): JsonResponse
    {
        $calendar = Calendar::query()->find($id);
        if ($calendar) {
            //Call Function Validator
            $valid = $this->validations($request);
            if (!$valid['Success']) {
                return $this->response(
                    data: $valid['Data'],
                    message: $valid['Message'],
                    errors: $valid['Errors'],
                    success: $valid['Success']
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
    public function deleteAppointment($id): JsonResponse
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
                "appointmentDate" => "required|date_format:Y-m-d H:i:s|after_or_equal:" . Carbon::now()->format('Y-m-d H:i:s'),
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
                "Data" => [],
                "Errors" => $validator->errors()->all(),
                "Message" => __('Schedule/Appointment.errors.validation'),
                "Success" => false
            ];
        }
        $work = new Carbon($request->get('appointmentDate'));

        //Validate Work Hour
        if (!(new Collection(config('Work.days')))->contains((new Carbon($request->get('appointmentDate')))->format('l'))
            || (($work->format("H:i") < config("Work.hours")[0]) || ($work->format("H:i") >= config("Work.hours")[1]))) {
            return [
                "Data" => [],
                "Errors" => [
                    __('Schedule/Appointment.errors.workDay')
                ],
                "Message" => __('Schedule/Appointment.errors.workDay'),
                "Success" => false,
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
                "Data" => [],
                "Errors" => [
                    __('Schedule/Appointment.errors.workDay')
                ],
                "Message" => __('Schedule/Appointment.errors.appointmentDate'),
                "Success" => false,
            ];
        }
        return ["Success" => true];
    }
}
