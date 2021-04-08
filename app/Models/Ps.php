<?php
/**
 * Ps
 */
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Relations\EmbedsMany;

/**
 * Ps
 */
class Ps extends Model {

    protected $connection = 'mongodb';

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
     * Get the professions for this Ps.
     */
    public function professions(): EmbedsMany
    {
        return $this->embedsMany(Profession::class);
    }

}
