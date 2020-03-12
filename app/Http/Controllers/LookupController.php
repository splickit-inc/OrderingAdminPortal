<?php namespace App\Http\Controllers;

use App\Model\OrganizationLookupMap;
use Illuminate\Http\Request;
use App\Model\PortalLookupHierarchy;

class LookupController extends Controller
{
    protected $lookupModel;
    protected $user_organization_id;

    public function __construct(OrganizationLookupMap $lookupMap)
    {
        $this->lookupModel = $lookupMap;

        $this->middleware(function ($request, $next) {
            $this->user_organization_id = session('user_organization_id');
            return $next($request);
        });

    }

    public function index($type_id_field)
    {
        $lookup_values = $this->getLookupByTypeIdField($this->user_organization_id, $type_id_field);
        return $lookup_values;
    }

    public function multiple(Request $request)
    {
        $typeIdFields = $request->all();
        $lookupsResult = [];
        $portal_organization_lookup = new PortalLookupHierarchy();

        foreach ($typeIdFields as $type_id_field) {
            $lookup_values = $portal_organization_lookup->getLookupByOrganizationPermissions($this->user_organization_id, $type_id_field);
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
        return $this->lookupModel->getLookupByOrganizationPermissions($user_organization_id, $type_id_field)->toArray();
    }
}