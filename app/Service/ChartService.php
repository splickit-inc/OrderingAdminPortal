<?php namespace App\Service;

use App\Model\Lookup;
use App\Model\OrganizationLookupMap;
use App\Model\PortalLookupHierarchy;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class ChartService {

    public function convertCountsToChartLabelsAndData($sql_response, $label_column, $data_column) {
        $response = [
          'labels' => [],
          'chart_values' => []
        ];

        foreach ($sql_response as $value) {
            $response['labels'][] = $value->{$label_column};
            $response['chart_values'][] = $value->{$data_column};
        }
        return $response;
    }
}
