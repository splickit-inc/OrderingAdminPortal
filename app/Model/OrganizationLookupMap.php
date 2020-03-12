<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrganizationLookupMap extends Model {
    protected $table = 'portal_organization_lookup_map';

    public function getLookupByOrganizationPermissions($organization_id, $type_id_field)
    {
        $query = $this->newQuery()
            ->join('Lookup', function ($join) use ($organization_id){
                $join->on('portal_organization_lookup_map.lookup_id', '=','Lookup.lookup_id')
                    ->where('portal_organization_lookup_map.organization_id','=',$organization_id);
            })
            ->where('Lookup.type_id_field','=',$type_id_field)
            ->select('Lookup.*');

        $result = $query->get();
        if($result->count() == 0)
        {
            $result = $this->newQuery()
                ->join('Lookup', function ($join) use ($organization_id,$type_id_field){
                    $join->on('portal_organization_lookup_map.type_id_field', '=','Lookup.type_id_field')
                        ->where('portal_organization_lookup_map.organization_id','=',$organization_id)
                        ->where('portal_organization_lookup_map.type_id_field', '=', $type_id_field)->whereNull('portal_organization_lookup_map.lookup_id');
                })->select('Lookup.*')->get();
        }
        return $result;
    }
}
