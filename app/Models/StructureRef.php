<?php


namespace App\Models;


use Jenssegers\Mongodb\Eloquent\Model;

class StructureRef extends Model {

    protected $connection = 'mongodb';

    protected $primaryKey = 'structureId';

    protected $fillable = [
        'structureId'
    ];

    /**
     * Get the structure associated with the structure reference.
     */
    public function structure()
    {
        return $this->hasOne(Structure::class, 'structureTechnicalId', 'structureId');
    }

}
