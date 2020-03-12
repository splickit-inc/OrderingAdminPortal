<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Holiday extends Model implements Auditable {

    use \OwenIt\Auditing\Auditable;

    protected $table = 'Holiday';
//    protected $primaryKey = 'holiday_id';
    protected $fillable = ['name'];

    public $timestamps = false;

    public function decodeOpenClose($value) {
        if ($value == 'O') {
            return true;
        }
        else {
            return false;
        }
    }

    public function decodeTrueFalseOpenClose($value) {
        if ($value) {
            return 'O';
        }
        else {
            return 'C';
        }
    }
}
