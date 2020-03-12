<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PortalLookupHierarchy extends Model {
    protected $table = 'portal_lookup_hierarchy';
    protected $lookupQuery = false;
    protected $type_id_field;

    public function getLookupByOrganizationPermissions($organization_id, $type_id_field, $brand_id = false)
    {
        $this->type_id_field = $type_id_field;

        if ($brand_id) {
            $brand_count = PortalLookupHierarchy::where('brand_id', '=', $brand_id)->where('lookup_type_id_field', '=', $type_id_field)->count();

            if ($brand_count > 0) {
                $this->setQueryToBrand($brand_id);
                return $this->lookupQuery->get()->toArray();
            }
        }

        $organization_lookup_count = PortalLookupHierarchy::where('organization_id', '=', $organization_id)->where('lookup_type_id_field', '=', $type_id_field)->count();


        if ($organization_lookup_count == 0) {

            $splickit_organization_lookup_count = PortalLookupHierarchy::where('organization_id', '=', 1)->where('lookup_type_id_field', '=', $type_id_field)->count();

            if ($splickit_organization_lookup_count == 0) {
                $this->setQueryToAllLookup();
                return $this->lookupQuery->get()->toArray();
            }
            else {
                $this->setQueryToSplickitOrg();
                return $this->lookupQuery->get()->toArray();
            }
        }
        else {
            $this->setQueryToOrganization($organization_id);
            return $this->lookupQuery->get()->toArray();
        }

    }

    protected function setQueryToAllLookup() {
        $this->lookupQuery = \DB::table('Lookup')
            ->where('Lookup.type_id_field','=',$this->type_id_field)
            ->where('Lookup.active','=', 'Y')
            ->orderBy('Lookup.type_id_name')
            ->select('Lookup.*');
    }

    protected function setQueryToBrand($brand_id) {
        $this->lookupQuery = \DB::table('portal_brand_lookup_map')
            ->join('Lookup', function ($join) use ($brand_id){
                $join->on('portal_brand_lookup_map.lookup_id', '=','Lookup.lookup_id')
                    ->where('portal_brand_lookup_map.brand_id','=', $brand_id
                    );
            })
            ->where('Lookup.type_id_field','=',$this->type_id_field)
            ->where('Lookup.active','=', 'Y')
            ->orderBy('Lookup.type_id_name')
            ->select('Lookup.*');
    }

    protected function setQueryToOrganization($organization_id) {
        $this->lookupQuery = \DB::table('portal_organization_lookup_map')
            ->join('Lookup', function ($join) use ($organization_id){
                $join->on('portal_organization_lookup_map.lookup_id', '=','Lookup.lookup_id')
                    ->where('portal_organization_lookup_map.organization_id','=', $organization_id
                    );
            })
            ->where('Lookup.type_id_field','=',$this->type_id_field)
            ->where('Lookup.active','=', 'Y')
            ->orderBy('Lookup.type_id_name')
            ->select('Lookup.*');
    }

    protected function setQueryToSplickitOrg($type_id_field) {
        $this->lookupQuery = \DB::table('portal_organization_lookup_map')
            ->join('Lookup', function ($join) {
                $join->on('portal_organization_lookup_map.lookup_id', '=','Lookup.lookup_id')
                    ->where('portal_organization_lookup_map.organization_id','=',1
                    );
            })
            ->where('Lookup.type_id_field','=',$type_id_field)
            ->where('Lookup.active','=', 'Y')
            ->orderBy('Lookup.type_id_name')
            ->select('Lookup.*');
    }
}