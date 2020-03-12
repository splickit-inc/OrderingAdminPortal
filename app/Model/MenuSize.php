<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MenuSize extends Model {

    protected $table = 'Sizes';
    protected $primaryKey = 'size_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
