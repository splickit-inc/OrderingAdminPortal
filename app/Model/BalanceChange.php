<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BalanceChange extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Balance_Change';
}
