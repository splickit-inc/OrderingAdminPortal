<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserBrand extends Model {

    protected $table = 'portal_user_brand';
    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['user_id', 'brand_id'];
}
