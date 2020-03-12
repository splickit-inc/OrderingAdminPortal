<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ObjectOwnership extends Model {

    protected $table = 'portal_object_ownership';
    protected $primaryKey = 'id';

    public function findAllChildRecords($object_type, $user_id){
        //$this::where()
    }
}
