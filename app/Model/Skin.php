<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Skin extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Skin';
    protected $primaryKey = 'skin_id';

    protected $fillable = ['brand_id', 'skin_id', 'skin_name', 'skin_description',
        'external_identifier', 'public_client_id', 'iphone_certificate_file_name',
        'lat', 'lng', 'zip', 'rules_info'];

    public $timestamps = false;

    /**
     * Creates a new Skin filling the basic fields with the Lead information
     *
     * @param Lead $lead
     * @param      $brandId
     * @return Skin
     */
    public static function fromLead(Lead $lead, $brandId)
    {
        $skin = new Skin();
        $identifier = 'com.yourbiz.' . $lead['subdomain'];
        $skin->fill(['brand_id' => $brandId,
            'external_identifier' => $identifier,
            'skin_name' => $lead['store_name'], 'skin_description' => $lead['store_name'],
            'public_client_id' => '', 'iphone_certificate_file_name' => "$identifier.pem",
            'lat' => '0', 'lng' => '0', 'zip' => $lead['store_zip']]);
        return $skin;
    }

    public function getSkinsByBrand($brand_id)
    {
        return static::where('brand_id', '=', $brand_id);
    }
}