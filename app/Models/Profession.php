<?php
/**
 * Profession
 */
namespace App\Models;


use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Relations\EmbedsMany;

/**
 * Profession
 */
class Profession extends Model {

    protected $connection = 'mongodb';

    protected $primaryKey = 'exProId';

    protected $fillable = [
        'exProId',
        'code',
        'categoryCode',
        'salutationCode',
        'lastName',
        'firstName',
        'expertises',
        'workSituations'
    ];

    /**
     * Get the Expertise list for this Profession.
     */
    public function expertises(): EmbedsMany
    {
        return $this->embedsMany(Expertise::class);
    }

    /**
     * Get the WorkSituation list for this Profession.
     */
    public function workSituations(): EmbedsMany
    {
        return $this->embedsMany(WorkSituation::class);
    }

}
