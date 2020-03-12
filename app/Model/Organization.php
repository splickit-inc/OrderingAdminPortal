<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Organization extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;
    protected $table = 'portal_organizations';
}
