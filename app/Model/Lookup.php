<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Lookup extends Model {

    protected $table = 'Lookup';
    public $timestamps = false;
    protected $primaryKey = 'lookup_id';
}