<?php

namespace App\Model;

class Offer extends BaseModel
{
    protected $table = 'portal_offers';
    protected $fillable = ['name', 'description'];
    protected $searchParams = ['name', 'description'];

    /**
     * Get the assets of the offer.
     */
    public function assets()
    {
        return $this->hasMany('App\Model\OfferAsset');
    }

    /**
     * Get the leads of the offer.
     */
    public function leads()
    {
        return $this->hasMany('App\Model\Lead');
    }
}
