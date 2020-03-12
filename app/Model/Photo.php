<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\LogicalDelete;
use OwenIt\Auditing\Contracts\Auditable;

class Photo extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Photo';

    protected $fillable = ['item_id', 'width', 'height'];

    protected $attributes = [
      'logical_delete' => 0,
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new LogicalDelete);
    }
}
