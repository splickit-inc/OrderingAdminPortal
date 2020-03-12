<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 12/15/17
 * Time: 12:00 PM
 */

namespace App\Service;


use Illuminate\Database\Query\Builder;
use Ramsey\Uuid\Uuid;

class ReportExportationService extends BaseService {

    /**
     * Exports a report to a csv file
     *
     * @param array   $headers
     * @param array   $selectedFields
     * @param Builder $query
     * @return bool|int
     */
    public function exportToCSV(array $headers, array $selectedFields, Builder $query) {
        $this->clearErrors();
        try {
            \Log::info('Top of exportToCSV');
            $path = sys_get_temp_dir() . '/admin_portal/reports';
            $fileName = Uuid::uuid4()->toString() . '.csv';
            $headerStr = implode(',', $headers);
            \Log::info('path: '.$path);
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $fullName = "$path/$fileName";
            \Log::info('REport DB: '.env('REPORTS_DB_DATABASE'));
            \Log::info('Full Name:'.$fullName);
            file_put_contents($fullName, $headerStr . PHP_EOL, FILE_APPEND | LOCK_EX);
            $allResults = $query->get()->toArray();
            \Log::info('allResults: '.json_encode($allResults));
            foreach ($allResults as $res) {
                $selectedValues = [];
                foreach ($selectedFields as $field) {
                    array_push($selectedValues, $this->sanitizeValue(((array)$res)[$field]));
                }
                $resStr = implode(',', $selectedValues);
                file_put_contents($fullName, $resStr . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            \Log::info('About to return fullName');
            return $fullName;
        } catch (\Exception $e) {
            \Log::info('Error Message:'.$e->getMessage());
            $this->addError($e->getMessage());
        }
        return false;
    }

    private function sanitizeValue($val) {
        if (!is_string($val)) {
            return $val;
        }
        if (strpos($val, '"') !== false || strpos($val, ',') !== false) {
            $val = str_replace('"', '""', $val);
            return "\"$val\"";
        }
        return $val;
    }
}