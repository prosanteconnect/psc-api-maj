<?php
/**
 * Ps
 */
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Relations\EmbedsMany;
use Jenssegers\Mongodb\Relations\HasMany;

/**
 * Ps
 */
class Ps extends Model {

    protected $connection = 'mongodb';

    protected $collection = 'ps';

    protected $primaryKey = 'nationalId';

    protected $fillable = [
        'idType',
        'id',
        'nationalId',
        'lastName',
        'firstName',
        'dateOfBirth',
        'birthAddressCode',
        'birthCountryCode',
        'birthAddress',
        'genderCode',
        'phone',
        'email',
        'salutationCode',
        'professions'
        ];

    /**
     * Get the reference ps for nationalId.
     */
    public function psRef(): HasMany
    {
        return $this->hasMany(PsRef::class, "nationalId", "nationalId");
    }

    /**
     * Get the professions for this Ps.
     */
    public function professions(): EmbedsMany
    {
        return $this->embedsMany(Profession::class);
    }

}
