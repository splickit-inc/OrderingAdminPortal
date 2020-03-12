<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OfferAsset extends Model
{
    protected $table = 'portal_offer_assets';
    protected $fillable = ['offer_id', 'description', 'type', 'value'];

    /**
     * Get the offer that owns the asset.
     */
    public function offer()
    {
        return $this->belongsTo('App\Model\Offer');
    }
}
