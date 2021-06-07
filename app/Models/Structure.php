<?php


namespace App\Models;


use Jenssegers\Mongodb\Eloquent\Model;

class Structure extends Model {

    protected $connection = 'mongodb';

    protected $collection = 'structure';

    protected $primaryKey = 'structureTechnicalId';

    protected $fillable = [
        'siteSIRET',
        'siteSIREN',
        'siteFINESS',
        'legalEstablishmentFINESS',
        'structureTechnicalId',
        'legalCommercialName', # raison sociale site
        'publicCommercialName', # enseigne commerciale site
        'recipientAdditionalInfo', # Complément destinataire
        'geoLocationAdditionalInfo', # Complément point géographique
        'streetNumber', # Numéro Voie
        'streetNumberRepetitionIndex', # Indice répétition voie
        'streetCategoryCode', # Code type de voie
        'streetLabel', # Libellé Voie
        'distributionMention', # Mention distribution
        'cedexOffice',
        'postalCode',
        'communeCode',
        'countryCode',
        'phone',
        'phone2',
        'fax',
        'email',
        'departmentCode',
        'oldStructureId',
        'registrationAuthority'
    ];

    /**
     * Get the structure reference associated with the reference.
     */
    public function structureRef()
    {
        return $this->hasOne(StructureRef::class, 'structureId', 'structureTechnicalId');
    }

}
