<?php

namespace App\Jobs;

use App\Models\Ps;

class AggregatePs extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Ps::raw(function($collection)
        {
            return $collection->aggregate([
                ['$unwind' => '$professions'],
                ['$unwind' => '$professions.expertises'],
                ['$unwind' => '$professions.workSituations'],
                ['$project' =>
                    [
                        '_id' => 0,
                        'nationalId' => 1,
                        'lastName' => 1,
                        'firstName' => 1,
                        'dateOfBirth' => 1,
                        'birthAddressCode' => 1,
                        'birthCountryCode' => 1,
                        'birthAddress' => 1,
                        'genderCode' => 1,
                        'phone' => 1,
                        'email' => 1,
                        'salutationCode' => '$professions.code',
                        'profession_code' => '$professions.categoryCode',
                        'profession_salutationCode'=> '$professions.salutationCode',
                        'profession_lastName'=> '$professions.lastName',
                        'profession_firstName'=> '$professions.firstName',
                        'profession_expertise_categoryCode'=> '$professions.expertises.categoryCode',
                        'profession_expertise_code'=> '$professions.expertises.code',
                        'profession_situation_modeCode'=> '$professions.workSituations.modeCode',
                        'profession_situation_activitySectorCode'=> '$professions.workSituations.activitySectorCode',
                        'profession_situation_pharmacistTableSectionCode'=> '$professions.workSituations.pharmacistTableSectionCode',
                        'profession_situation_roleCode'=> '$professions.workSituations.roleCode',
                        'structure_siteSIRET'=> '$professions.workSituations.structure.siteSIRET',
                        'structure_siteSIREN'=> '$professions.workSituations.structure.siteSIREN',
                        'structure_siteFINESS'=> '$professions.workSituations.structure.siteFINESS',
                        'structure_legalEstablishmentFINESS'=> '$professions.workSituations.structure.legalEstablishmentFINESS',
                        'structure_structureTechnicalId'=> '$professions.workSituations.structure.structureTechnicalId',
                        'structure_legalCommercialName'=> '$professions.workSituations.structure.legalCommercialName',
                        'structure_publicCommercialName'=> '$professions.workSituations.structure.publicCommercialName',
                        'structure_recipientAdditionalInfo'=> '$professions.workSituations.structure.recipientAdditionalInfo',
                        'structure_geoLocationAdditionalInfo'=> '$professions.workSituations.structure.geoLocationAdditionalInfo',
                        'structure_streetNumber'=> '$professions.workSituations.structure.streetNumber',
                        'structure_streetNumberRepetitionIndex'=> '$professions.workSituations.structure.streetNumberRepetitionIndex',
                        'structure_streetCategoryCode'=> '$professions.workSituations.structure.streetCategoryCode',
                        'structure_streetLabel'=> '$professions.workSituations.structure.streetLabel',
                        'structure_distributionMention'=> '$professions.workSituations.structure.distributionMention',
                        'structure_cedexOffice'=> '$professions.workSituations.structure.cedexOffice',
                        'structure_postalCode'=> '$professions.workSituations.structure.postalCode',
                        'structure_communeCode'=> '$professions.workSituations.structure.communeCode',
                        'structure_countryCode'=> '$professions.workSituations.structure.countryCode',
                        'structure_phone'=> '$professions.workSituations.structure.phone',
                        'structure_phone2'=> '$professions.workSituations.structure.phone2',
                        'structure_fax'=> '$professions.workSituations.structure.fax',
                        'structure_email'=> '$professions.workSituations.structure.email',
                        'structure_departmentCode'=> '$professions.workSituations.structure.departmentCode',
                        'structure_oldStructureId'=> '$professions.workSituations.structure.oldStructureId',
                        'structure_registrationAuthority'=> '$professions.workSituations.structure.registrationAuthority',
                    ],
                ],
                [ '$out' => 'extractRass'],
            ]);
        });
    }
}
