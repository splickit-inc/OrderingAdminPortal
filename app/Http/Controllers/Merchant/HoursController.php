<?php namespace App\Http\Controllers\Merchant;

use App\Model\Merchant;
use Carbon\Carbon;
use \Exception;
use \App\Http\Controllers\Controller;
use \App\Model\Holiday;
use \App\Model\HolidayHour;
use App\Model\OperationHour;
use \App\Model\Hour;
use \App\Service\Utility;
use Illuminate\Http\Request;

class HoursController extends Controller
{

    public function index(Merchant $model)
    {
        try {
            $merchant_id = session('current_merchant_id');

            /** @var Merchant $model */
            $model = $model->find($merchant_id);
            if (empty($model)) {
                throw new \Exception('please select the current merchant first', 404);
            }

            $utility = new Utility();

            $hours = Hour::where('merchant_id', '=', $merchant_id)
                ->where('hour_type', '=', 'R')->orderBy('day_of_week')->get();

            $hours = $this->formatHoursForHuman($hours);

            $delivery_hours = Hour::where('merchant_id', '=', $merchant_id)
                ->where('hour_type', '=', 'D')->orderBy('day_of_week')->get();

            $delivery_hours = $this->formatHoursForHuman($delivery_hours);


            $holiday_hours = HolidayHour::where([
                ['merchant_id', '=', $merchant_id],
                ['the_date', '>=', date('Y').'-1-1']
            ])->get()->toArray();

            try {
                $holiday = Holiday::where('merchant_id', '=', $merchant_id)->firstOrFail();
                $holiday = $holiday->toArray();
            } catch (Exception $ex) {
                $holiday = ['newyearsday' => null, 'easter' => null, 'fourthofjuly' => null, 'thanksgiving' => null, 'christmas' => null];
            }

            $full_holiday_hours = [];
            $custom_holiday_hours = [];

            $current_year = date('Y');

            $holiday_dates = [
                'newyearsday' => [
                    'set' => false
                ],
                'easter' => [
                    'set' => false
                ],
                'new_years' => [
                    'set' => false
                ]
            ];

            foreach ($holiday_hours as $index => $holiday_hour) {
                $day = date('m-d', strtotime($holiday_hour['the_date']));
                $holiday_year = date('Y', strtotime($holiday_hour['the_date']));

                if ($holiday_year == $current_year) {
                    switch ($day) {
                        case '01-01':
                            $full_holiday_hours[$index]['day_id'] = 'newyearsday';
                            $full_holiday_hours[$index]['holiday_id'] = $holiday_hour['holiday_id'];
                            $full_holiday_hours[$index]['day_name'] = 'New Years';

                            $full_holiday_hours[$index]['day_open'] = $utility->convertYNBoolean($holiday_hour['day_open']);
                            $full_holiday_hours[$index]['open'] = date('h:i', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['open_am_pm'] = date('a', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['close'] = date('h:i', strtotime($holiday_hour['close']));
                            $full_holiday_hours[$index]['close_am_pm'] = date('a', strtotime($holiday_hour['close']));

                            break;
                        case date("m-d", easter_date()):
                            $full_holiday_hours[$index]['day_id'] = 'easter';
                            $full_holiday_hours[$index]['holiday_id'] = $holiday_hour['holiday_id'];
                            $full_holiday_hours[$index]['day_name'] = 'Easter';

                            $full_holiday_hours[$index]['day_open'] = $utility->convertYNBoolean($holiday_hour['day_open']);
                            $full_holiday_hours[$index]['open'] = date('h:i', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['open_am_pm'] = date('a', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['close'] = date('h:i', strtotime($holiday_hour['close']));
                            $full_holiday_hours[$index]['close_am_pm'] = date('a', strtotime($holiday_hour['close']));

                            break;
                        case '07-04':
                            $full_holiday_hours[$index]['day_id'] = 'fourthofjuly';
                            $full_holiday_hours[$index]['holiday_id'] = $holiday_hour['holiday_id'];
                            $full_holiday_hours[$index]['day_name'] = '4th of July';

                            $full_holiday_hours[$index]['day_open'] = $utility->convertYNBoolean($holiday_hour['day_open']);
                            $full_holiday_hours[$index]['open'] = date('h:i', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['open_am_pm'] = date('a', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['close'] = date('h:i', strtotime($holiday_hour['close']));
                            $full_holiday_hours[$index]['close_am_pm'] = date('a', strtotime($holiday_hour['close']));

                            break;
                        case date('m-d', strtotime('fourth thursday of november ' . date("Y"))):
                            $full_holiday_hours[$index]['day_id'] = 'thanksgiving';
                            $full_holiday_hours[$index]['holiday_id'] = $holiday_hour['holiday_id'];
                            $full_holiday_hours[$index]['day_name'] = 'Thanksgiving';

                            $full_holiday_hours[$index]['day_open'] = $utility->convertYNBoolean($holiday_hour['day_open']);;
                            $full_holiday_hours[$index]['open'] = date('h:i', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['open_am_pm'] = date('a', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['close'] = date('h:i', strtotime($holiday_hour['close']));
                            $full_holiday_hours[$index]['close_am_pm'] = date('a', strtotime($holiday_hour['close']));

                            break;
                        case '12-25':
                            $full_holiday_hours[$index]['day_id'] = 'christmas';
                            $full_holiday_hours[$index]['holiday_id'] = $holiday_hour['holiday_id'];
                            $full_holiday_hours[$index]['day_name'] = 'Christmas';

                            $full_holiday_hours[$index]['day_open'] = $utility->convertYNBoolean($holiday_hour['day_open']);;
                            $full_holiday_hours[$index]['open'] = date('h:i', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['open_am_pm'] = date('a', strtotime($holiday_hour['open']));
                            $full_holiday_hours[$index]['close'] = date('h:i', strtotime($holiday_hour['close']));
                            $full_holiday_hours[$index]['close_am_pm'] = date('a', strtotime($holiday_hour['close']));

                            break;

                        default:
                            $custom_holiday_hours[$index]['day_open'] = $utility->convertYNBoolean($holiday_hour['day_open']);
                            $custom_holiday_hours[$index]['holiday_id'] = $holiday_hour['holiday_id'];
                            $custom_holiday_hours[$index]['open'] = date('h:i', strtotime($holiday_hour['open']));
                            $custom_holiday_hours[$index]['open_am_pm'] = date('a', strtotime($holiday_hour['open']));
                            $custom_holiday_hours[$index]['close'] = date('h:i', strtotime($holiday_hour['close']));
                            $custom_holiday_hours[$index]['close_am_pm'] = date('a', strtotime($holiday_hour['close']));
                            $custom_holiday_hours[$index]['display_date'] = date('m/d', strtotime($holiday_hour['the_date']));
                    }
                }
            }

            $midDayHours = $model->holeHours()->get();

            return response()->json([
                'hours' => $hours,
                'delivery_hours' => $delivery_hours,
                'holiday' => $holiday,
                'holiday_hours' => array_values($holiday_hours),
                'full_holiday_hours' => $full_holiday_hours,
                'custom_holiday_hours' => array_values($custom_holiday_hours),
                'mid_day_hours' => $midDayHours
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['errors' => $exception->getMessage()], $exception->getCode());
        }
    }


    public function updateStoreHours(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $this->saveHoursDB($data['hours'], $merchant_id, 'R');

        return 1;
    }

    public function updateDeliveryHours(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $this->saveHoursDB($data['delivery_hours'], $merchant_id, 'D');

        return 1;
    }


    //Updates Holiday (Open/Close)
    public function updateHoliday(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $holiday = Holiday::firstOrNew(['merchant_id' => $merchant_id]);

        $holiday->merchant_id = $merchant_id;

        $holiday->newyearsday = $data['newyearsday'];
        $holiday->easter = $data['easter'];
        $holiday->fourthofjuly = $data['fourthofjuly'];
        $holiday->thanksgiving = $data['thanksgiving'];
        $holiday->christmas = $data['christmas'];

        $holiday->save();

        return 1;
    }

    public function saveHoursDB($hours, $merchant_id, $hour_type)
    {
        foreach ($hours as $index => $data_hour) {

            //Get Tomorrows Index to set "First Close"
            if ($index == 0) {
                $yesterday_index = 6;
            } else {
                $yesterday_index = $index - 1;
            }

            //Get Yesterday's Close to see if we will do a second close for today
            $close_24_hour_yesterday = date("H:i", strtotime($hours[$yesterday_index]['close_human'] . " " . $hours[$yesterday_index]['close_am_pm']));

            //Post Open in 24 Hour Format Today
            $close_24_hour = date("H:i", strtotime($data_hour['close_human'] . " " . $data_hour['close_am_pm']));

            $close = null;
            $second_close = null;

            //See if Yesterday Close is early morning rather than late PM
            if (date("H", strtotime($close_24_hour_yesterday)) < 8) {
                $close = $close_24_hour_yesterday;

                if ($close_24_hour > 8) {
                    $second_close = $close_24_hour;
                }
                else {
                    $second_close = null;
                }
            }
            else {
                if ($close_24_hour > 8) {
                    $close = $close_24_hour;
                }
            }

            //Post Open in 24 Hour Format Today
            $db_open = date("H:i", strtotime($data_hour['open_human'] . " " . $data_hour['open_am_pm']));

            //Create DB Record
            $hour = Hour::firstOrNew(['merchant_id' => $merchant_id, 'day_of_week' => $data_hour['day_of_week'], 'hour_type' => $hour_type]);

            $hour->merchant_id = $merchant_id;

            $hour->hour_type = $hour_type;
            $hour->day_of_week = $data_hour['day_of_week'];
            $hour->open = $db_open;
            $hour->close = $close;
            $hour->second_close = $second_close;
            $hour->day_open = $data_hour['day_open'] == true ? 'Y' : 'N';

            $hour->save();

            //Create Operation Hours Record

            //Post Open in 24 Hour Format Today
            $operation_open = date("H:i", strtotime($data_hour['open_human'] . " " . $data_hour['open_am_pm']));

            //Post Open in 24 Hour Format Today
            $operation_close = date("H:i", strtotime($data_hour['close_human'] . " " . $data_hour['close_am_pm']));

            $operation_hour = OperationHour::firstOrNew(['merchant_id' => $merchant_id, 'day_of_week' => $data_hour['day_of_week'], 'hour_type' => $hour_type]);

            $operation_hour->merchant_id = $merchant_id;

            $operation_hour->hour_type = $hour_type;
            $operation_hour->day_of_week = $data_hour['day_of_week'];
            $operation_hour->open = $operation_open;
            $operation_hour->close = $operation_close;

            $operation_hour->save();
        }
    }

    public function createHolidayHours(Request $request)
    {
        $data = $request->all();
        $utility = new Utility();

        $merchant_id = session('current_merchant_id');

        $day = date("Y-m-d", strtotime($data['other_day']));

        $holiday_hour = new HolidayHour();

        $holiday_hour->merchant_id = $merchant_id;

        $holiday_hour->the_date = $day;

        $holiday_hour->day_open = $utility->convertBooleanYN(!$data['closed_all_day']);

        $holiday_hour->open = date("H:i", strtotime($data['open']));
        $holiday_hour->close = date("H:i", strtotime($data['close']));

        $holiday_hour->second_close = null;

        $holiday_hour->save();

        $holiday_hour = $holiday_hour->toArray();

        $holiday_hour['display_date'] = date('m/d', strtotime($holiday_hour['the_date']));

        return $holiday_hour;
    }

    public function deleteHolidayHours($id)
    {
        HolidayHour::destroy($id);
        return 1;
    }

    public function formatHoursForHuman($hours)
    {
        foreach ($hours as $index => $hour) {
            if ($index == 6) {
                $tomorrow_index = 1;
            } else {
                $tomorrow_index = $index + 1;
            }

            //If Tomorrow/the next day has 2 closes, we know tomorrow's first close was actually today's early morning hour close
            if (!is_null($hour->second_close)) {
                if (!is_null($hours[$tomorrow_index]->second_close)) {
                    $hour->close_am_pm = date('a', strtotime($hours[$tomorrow_index]->close));
                    $hour->close_human = date('h:i', strtotime($hours[$tomorrow_index]->close));
                }
                else {
                    $hour->close_am_pm = date('a', strtotime($hour->second_close));
                    $hour->close_human = date('h:i', strtotime($hour->second_close));
                }

            } else {
                if (!is_null($hours[$tomorrow_index]->second_close)) {
                    $hour->close_am_pm = date('a', strtotime($hours[$tomorrow_index]->close));
                    $hour->close_human = date('h:i', strtotime($hours[$tomorrow_index]->close));
                }
                else {
                    $hour->close_am_pm = date('a', strtotime($hour->close));
                    $hour->close_human = date('h:i', strtotime($hour->close));
                }
            }
            $hour->open_am_pm = date('a', strtotime($hour->open));
            $hour->open_human = date('h:i', strtotime($hour->open));
        }

        if (sizeof($hours) < 7) {
            $day = 1;
            while ($day <= 7) {
                $hours[] = ['day_of_week' => $day,
                    'open' => "07:00:00",
                    'close' => "20:00:00",
                    'second_close' => null,
                    'close_am_pm' => "pm",
                    'close_human' => "08:00",
                    'open_am_pm' => "am",
                    'open_human' => "07:00"];
                $day++;
            }
        }
        return $hours->toArray();
    }

    public function updateStandardHolidayHours(Request $request)
    {
        $data = $request->all();
        $utility = new Utility();

        $holiday = HolidayHour::find($data['holiday_id']);

        if ($data['closed_all_day']) {
            $holiday['day_open'] = 'N';
        } else {
            $holiday['day_open'] = 'Y';
        }

        $db_open = date("H:i:s", strtotime($data['open']));
        $holiday->open = $db_open;

        $db_close = date("H:i:s", strtotime($data['close']));
        $holiday->close = $db_close;

        $holiday->save();

        return 1;
    }

    public function getMidDayClosures(Merchant $model)
    {
        try {
            $merchant_id = session('current_merchant_id');
            /** @var Merchant $model */
            $model = $model->find($merchant_id);
            if (empty($model)) {
                throw new \Exception('please select the current merchant first', 404);
            }

            $hours = $model->holeHours()->get();
            return response()->json(['hours' => $hours], 200);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['errors' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function addOrUpdateMidDayClosures(Request $request, Merchant $model)
    {
        try {
            $data = $request->all();
            $merchant_id = session('current_merchant_id');
            /** @var Merchant $model */
            $model = $model->find($merchant_id);
            if (empty($model)) {
                throw new \Exception('please select the current merchant first', 404);
            }

            $hour = $model->addOrUpdateMidDayHour($data);
            return response()->json($hour, 200);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['errors' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function deleteMidDayClosures($hour_id, Merchant $model)
    {
        try {
            $merchant_id = session('current_merchant_id');
            /** @var Merchant $model */
            $model = $model->find($merchant_id);
            if (empty($model)) {
                throw new \Exception('please select the current merchant first', 404);
            }

            $hour = $model->removeMidDayHour($hour_id);
            return response()->json($hour, 200);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['errors' => $exception->getMessage()], $exception->getCode());
        }
    }
}



