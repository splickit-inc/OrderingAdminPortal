<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MerchantMenuMap extends Model implements Auditable  {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Merchant_Menu_Map';
    protected $primaryKey = 'map_id';

    protected $fillable = ['merchant_id', 'menu_id','merchant_menu_type'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'created';

    function setRelatedMerchantMenuConfiguration($merchant_id, $menus) {
        static::where('merchant_id', '=', $merchant_id)->delete();
        if (count($menus) == 0) {
            return [];
        }
        $response = [];

        foreach ($menus as $menu) {
            if($menu['pivot']['merchant_menu_type'] == 'both')
            {
                $response = $this->createWithResponse($merchant_id,$menu['menu_id'],'pickup',$response);
                $response = $this->createWithResponse($merchant_id,$menu['menu_id'],'delivery',$response);
            }
            else {
                $response = $this->createWithResponse($merchant_id,$menu['menu_id'],$menu['pivot']['merchant_menu_type'],$response);
            }
        }
        return $response;
    }

    function createWithResponse($merchant_id, $menu_id, $merchant_menu_type, $response)
    {
        $result = static::create(
            ['merchant_id' => $merchant_id, 'menu_id' => $menu_id,'merchant_menu_type' => $merchant_menu_type]
        );
        array_push($response,$result);
        return $response;
    }
}
