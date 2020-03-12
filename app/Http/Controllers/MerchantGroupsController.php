<?php

namespace App\Http\Controllers;

use App\Service\LeadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Model\PortalMerchantGroups;
use App\Model\PortalMerchantGroupMerchantMap;
use App\Model\PortalMerchantGroupGroupMap;
use Illuminate\Support\Facades\Log;
use \App\Model\ObjectOwnership;
use \DB;

class MerchantGroupsController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->all();

        $newMerchantGroup = new PortalMerchantGroups();

        $newMerchantGroup->name = $data['name'];
        $newMerchantGroup->group_type = $data['type'];

        if (session('user_visibility') == 'brand') {
            $newMerchantGroup->brand_id = session('brand_manager_brand');
        }

        $newMerchantGroup->save();

        //Create Ownership Record
        $new_object_ownership = new ObjectOwnership();

        $new_object_ownership->user_id = Auth::user()->id;
        $new_object_ownership->organization_id = session('user_organization_id');
        $new_object_ownership->object_type = 'merchant_grp';
        $new_object_ownership->object_id = $newMerchantGroup->id;

        $new_object_ownership->save();



        if ($data['type'] == 'merchant') {
            foreach ($data['merchants'] as $merchant) {
                $group_merchant_map = new PortalMerchantGroupMerchantMap();
                $group_merchant_map->group_id = $newMerchantGroup->id;
                $group_merchant_map->merchant_id = $merchant['merchant_id'];

                $group_merchant_map->save();
            }
        } elseif ($data['type'] == 'group') {
            foreach ($data['groups'] as $group) {
                $group_group_map = new PortalMerchantGroupGroupMap();
                $group_group_map->parent_group_id = $newMerchantGroup->id;
                $group_group_map->child_group_id = $group['id'];
                $group_group_map->save();

                $newly_owned_portal_merchant_group = PortalMerchantGroups::find($group['id']);
                $newly_owned_portal_merchant_group->group_parent_id = $newMerchantGroup->id;
                $newly_owned_portal_merchant_group->save();
            }
        }
    }

    public function index()
    {
        $portal_merchant_groups = new PortalMerchantGroups();

        if (session('user_visibility') == 'global') {
            $result_merchant_groups = $portal_merchant_groups->loadGlobal();
        } elseif (session('user_visibility') == 'mine_only') {
            $result_merchant_groups = $portal_merchant_groups->loadMineOnly();
        } elseif (session('user_visibility') == 'operator') {
            $result_merchant_groups = $portal_merchant_groups->loadOperator();
        } elseif (session('user_visibility') == 'all') {
            $result_merchant_groups = $portal_merchant_groups->loadAll();
        } elseif (session('user_visibility') == 'brand') {
            $result_merchant_groups = $portal_merchant_groups->loadBrand();
        }
        return $result_merchant_groups;
    }

    public function searchResults($data, $portal_merchant_groups)
    {

        \DB::enableQueryLog();
        if (session('user_visibility') == 'global') {
            $result_merchant_groups = $portal_merchant_groups->searchGlobal($data['search_text']);
        } elseif (session('user_visibility') == 'mine_only') {
            $result_merchant_groups = $portal_merchant_groups->searchMineOnly($data['search_text']);
        } elseif (session('user_visibility') == 'operator') {
            $result_merchant_groups = $portal_merchant_groups->searchOperator($data['search_text']);
        } elseif (session('user_visibility') == 'all') {
            $result_merchant_groups = $portal_merchant_groups->searchOrganizationAll($data['search_text']);
        } elseif (session('user_visibility') == 'brand') {
            $result_merchant_groups = $portal_merchant_groups->searchBrand($data['search_text']);
        }
        $q = \DB::getQueryLog();
        return $result_merchant_groups;
    }

    public function search(Request $request)
    {
        $data = $request->all();

        $portal_merchant_groups = new PortalMerchantGroups();
        $portal_merchant_groups->potential_children_only = true;

        return $this->searchResults($data, $portal_merchant_groups);
    }

    public function searchAll(Request $request)
    {
        $data = $request->all();

        $portal_merchant_groups = new PortalMerchantGroups();

        return $this->searchResults($data, $portal_merchant_groups);
    }

    public function destroy($merchant_group_id)
    {
        PortalMerchantGroupMerchantMap::where('group_id', '=', $merchant_group_id)->delete();
        PortalMerchantGroupGroupMap::where('parent_group_id', '=', $merchant_group_id)->delete();

        ObjectOwnership::where('object_id', '=', $merchant_group_id)->where('object_type', '=', 'merchant_grp')->delete();

        PortalMerchantGroups::destroy($merchant_group_id);

        return 1;
    }

    public function setCurrent($merchant_group_id)
    {
        session(['merchant_group_id' => $merchant_group_id]);
    }

    public function show()
    {
        $merchant_group = PortalMerchantGroups::find(session('merchant_group_id'))->toArray();

        if ($merchant_group['group_type'] == 'merchant') {
            $merchants = DB::table('portal_merchant_group_merchant_map')
                ->join('Merchant', 'portal_merchant_group_merchant_map.merchant_id', '=', 'Merchant.merchant_id')
                ->where('portal_merchant_group_merchant_map.group_id', '=', $merchant_group['id'])
                ->get(['Merchant.*'])
                ->toArray();

            $groups = [];
        } else {
            $groups = DB::table('portal_merchant_group_group_map')
                ->join('portal_merchant_groups', 'portal_merchant_group_group_map.child_group_id', '=', 'portal_merchant_groups.id')
                ->where('portal_merchant_group_group_map.parent_group_id', '=', $merchant_group['id'])
                ->get(['portal_merchant_groups.*'])
                ->toArray();

            $merchants = [];
        }

        return ['merchant_group' => $merchant_group, 'merchants' => $merchants, 'groups' => $groups];
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $merchant_group = PortalMerchantGroups::find(session('merchant_group_id'));
        $merchant_group->name = $data['name'];
        $merchant_group->save();

        if ($data['group_type'] == 'merchant') {
            if (isset($data['merchants'])) {
                PortalMerchantGroupMerchantMap::where('group_id', '=', session('merchant_group_id'))->delete();

                foreach ($data['merchants'] as $merchant) {
                    $groupMerchantMap = new PortalMerchantGroupMerchantMap();
                    $groupMerchantMap->group_id = session('merchant_group_id');
                    $groupMerchantMap->merchant_id = $merchant['merchant_id'];

                    $groupMerchantMap->save();
                }
            }
        } elseif ($data['group_type'] == 'group') {
            if (isset($data['groups'])) {
                $old_group_records = PortalMerchantGroupGroupMap::where('parent_group_id', '=', session('merchant_group_id'))->get();

                foreach ($old_group_records as $old_group) {
                    $old_child_group = PortalMerchantGroups::find($old_group->child_group_id);
                    $old_child_group->group_parent_id = null;
                    $old_child_group->save();
                }


                foreach ($data['groups'] as $group) {
                    $group_group_map = new PortalMerchantGroupGroupMap();
                    $group_group_map->parent_group_id = session('merchant_group_id');
                    $group_group_map->child_group_id = $group['id'];
                    $group_group_map->save();

                    $newly_owned_portal_merchant_group = PortalMerchantGroups::find($group['id']);
                    $newly_owned_portal_merchant_group->group_parent_id = session('merchant_group_id');
                    $newly_owned_portal_merchant_group->save();
                }
            }

        }
    }

    /**
     * @param $group_id
     * @param PortalMerchantGroups $model
     * @return Response
     */
    public function getMerchants($group_id, PortalMerchantGroups $model)
    {
        $merchantGroup = $model->find($group_id);

        if(empty($merchantGroup))
        {
            return response()->json('Merchant Group not Found', 404);
        }

        return response()->json(['merchants' => $merchantGroup->merchants()->get()], 200);
    }
}