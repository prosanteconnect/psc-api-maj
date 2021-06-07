<?php
/**
 * WorkSituation
 */
namespace App\Models;


use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Relations\EmbedsMany;

/**
 * WorkSituation
 */
class WorkSituation extends Model {

    protected $connection = 'mongodb';

    protected $primaryKey = 'situId';

    protected $fillable = [
        'situId',
        'modeCode',
        'activitySectorCode',
        'pharmacistTableSectionCode',
        'roleCode',
        'structures'
    ];

    /**
     * Get the Structures for this WorkSituation.
     */
    public function structures(): EmbedsMany
    {
        return $this->embedsMany(StructureRef::class);
    }

}
