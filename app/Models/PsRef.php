<?php
/**
 * PsRef
 */
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * PsRef
 */
class PsRef extends Model {

    protected $connection = 'mongodb';

    protected $collection = 'psref';

    protected $primaryKey = 'nationalIdRef';

    protected $fillable = [
        'nationalIdRef',
        'nationalId',
        'activated',
        'deactivated'
        ];

    /**
     * Get the ps this nationalId is pointing to.
     */
    public function ps()
    {
        return $this->belongsTo(Ps::class, 'nationalId', 'nationalId');
    }
}
