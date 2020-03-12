<?php namespace App\Service;

use App\Model\Lookup;
use App\Model\OrganizationLookupMap;
use App\Model\PortalLookupHierarchy;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class Utility {

    public function convertBooleanYN($value) {
        if ($value == true || $value == 'Y') {
            return "Y";
        } else {
            return "N";
        }
    }

    public function convertYNBoolean($value) {
        if ($value == 'Y') {
            return true;
        } else {
            return false;
        }
    }

    public function converOneZeroNBoolean($value) {
        if ($value == '1') {
            return true;
        } else {
            return false;
        }
    }

    //Converts a holiday to an actual date
    public function convertHolidayToDate($day) {
        if ($day == 'New Years') {
            $next_year = date("Y") + 1;
            return $next_year . "-01-01";
        } elseif ($day == 'Easter') {
            return date("Y-m-d", easter_date());
        } elseif ($day == 'July 4th') {
            return date("Y") . "-07-04";
        } elseif ($day == 'Thanksgiving') {
            return date('Y-m-d', strtotime('fourth thursday of november ' . date("Y")));
        } elseif ($day == 'Christmas') {
            return date("Y") . "-12-25";
        }
    }

    //This allows you to find the specific property value in an array with another property value
    public function findArrayPropertyValueWithProperty($array, $search_property, $value, $return_property) {
        foreach ($array as $element) {
            if (is_object($element)) {
                $element = json_decode(json_encode($element), True);
            }
            if ($element[$search_property] == $value) {
                return $element[$return_property];
                break;
            }
        }
    }

    //This allows you to find the object in an array with another property value
    public function returnArrayObjectWithProperty($array, $search_property, $value) {
        foreach ($array as $element) {
            if ($element[$search_property] == $value) {
                return $element;
                break;
            }
        }
        return false;
    }

    public function getLookupValues($typeIdFields) {
        $lookupsResult = [];

        foreach ($typeIdFields as $type_id_field) {
            $lookup_values = $this->getLookupByTypeIdField(session('user_organization_id'), $type_id_field);
            $lookupsResult[$type_id_field] = $lookup_values;
        }

        //Put the Continental U.S. Timezones on Top
        if (isset($lookupsResult['time_zone'])) {
            $us_timezones = [];
            $non_us_timezones = [];

            foreach ($lookupsResult['time_zone'] as $time_zone) {
                if ((float)$time_zone->type_id_value < -4.5 && (float)$time_zone->type_id_value > -9) {
                    $us_timezones[] = $time_zone;
                } else {
                    $non_us_timezones[] = $time_zone;
                }
            }
            $lookupsResult['time_zone'] = array_merge($us_timezones, $non_us_timezones);
        }
        return $lookupsResult;
    }

    protected function getLookupByTypeIdField($user_organization_id, $type_id_field)
    {
        /** @var OrganizationLookupMap $lookupModel */
        $lookupModel = App::make(PortalLookupHierarchy::class);
        return $lookupModel->getLookupByOrganizationPermissions($user_organization_id, $type_id_field);
    }

    public function getLookupRecordByTypeAndName($lookup_type, $lookup_name) {
        $type_array = [$lookup_type];
        if ($records = $this->getLookupValues($type_array)) {
            foreach ($records[$lookup_type] as $lookup_record) {
                if ($lookup_record['type_id_name'] == $lookup_name) {
                    return $lookup_record;
                }
            }
        }
    }

    public function getLookupValueByTypeAndName($lookup_type, $lookup_name) {
        if ($record = $this->getLookupRecordByTypeAndName($lookup_type, $lookup_name)) {
            return $record['type_id_value'];
        }
    }

    public function getImageExtension($image_name) {
        $jpg_count = substr_count(strtoupper($image_name), '.JPG');
        $jpeg_count = substr_count(strtoupper($image_name), '.JPEG');
        $png_count = substr_count(strtoupper($image_name), '.PNG');
        $gif_count = substr_count(strtoupper($image_name), '.GIF');

        if ($jpg_count > 0 || $jpeg_count > 0) {
            $extension = "jpg";
        } elseif ($png_count > 0) {
            $extension = "png";
        } elseif ($gif_count > 0) {
            $extension = "gif";
        }

        return $extension;
    }

    public function maxAttributeInArrayOfObjects($array, $prop) {
        if (sizeof($array) != 0) {
            return max(array_map(function ($o) use ($prop) {
                return $o->$prop;
            },
                $array));
        } else {
            return 0;
        }
    }

    public function minAttributeInArrayOfObjects($array, $prop) {
        if (sizeof($array) != 0) {
            return min(array_map(function ($o) use ($prop) {
                return $o->$prop;
            },
                $array));
        } else {
            return 0;
        }
    }

    public function convert24HourToAmPmValue($hour) {
        if ($hour < 13) {
            return $hour . "am";
        } else {
            $hour = $hour - 12;
            return $hour . "pm";
        }
    }

    function replaceEverythingButNotNumber($number) {
        return preg_replace("/[^0-9]/", '', $number);
    }

    function formatGoodTenDigitPhoneNumber($phone_number) {
        if (strlen($phone_number) != 10) {
            return $phone_number;
        }
        return substr($phone_number, 0, 3) . ' ' . substr($phone_number, 3, 3) . ' ' . substr($phone_number, 6, 4);
    }

    public function serverUrl() {
        return self::currentServerUrl();
    }

    public static function currentServerUrl(){
        if (!isset($_SERVER['SERVER_NAME'])) {
            return Config::get('app.url');
        }
        $protocol = (isset($_SERVER['HTTPS']) && (strcasecmp('off', $_SERVER['HTTPS']) !== 0)) ?
            'https://' : 'http://';
        $hostname = $_SERVER['SERVER_NAME'];
        $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null;
        if (!is_null($port) && !empty($port)) {
            $port = ':' . $port;
        }
        $url = $protocol . $hostname . $port;
        return $url;
    }

    public function getTextInBetweenStrings($text, $start, $end) {
        if ($end == '"') {
            $text = str_replace('"', "*", $text);
            $end = "*";
        }

        $startToEnd = substr($text, strpos($text, $start));
        return substr($startToEnd, strpos($startToEnd, $start) + strlen($start), strpos($startToEnd, $end) -strlen($start) );
    }

    public function validateDatetime($date_string) {
        return (bool)strtotime($date_string);
    }

    public function convertOffsetToTimeZoneString($offset) {
        if (date('I') != '0') {
            $offset = $offset + 1;
        }

        if (date('I') == '0') {
            if ($offset == -4) {
                return 'America/New_York';
            }
            elseif ($offset == -5) {
                return 'America/Chicago';
            }
            elseif ($offset == -6) {
                return 'America/Denver';
            }
            elseif ($offset == -6) {
                return 'America/Los_Angeles';
            }
        }
    }
}
