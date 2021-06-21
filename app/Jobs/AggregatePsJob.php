<?php

namespace App\Jobs;

use App\Models\Ps;

class AggregatePsJob extends Job
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
        Ps::query()->raw(function($collection)
        {
            return $collection->aggregate([
                ['$unwind' => '$professions'],
                ['$unwind' => '$professions.expertises'],
                ['$unwind' => '$professions.workSituations'],
                ['$unwind' => '$professions.workSituations.structures'],
                ['$lookup' => [
                        'from' => 'structure',
                        'localFiled' => 'professions.workSituations.structures.structureId',
                        'foreignField' => 'structureTechnicalId',
                        'as' => 'thisStructure'
                    ]
                ],
                ['$unwind' => '$thisStructure'],
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
                        'profession_expertise_typeCode'=> '$professions.expertises.typeCode',
                        'profession_expertise_code'=> '$professions.expertises.code',
                        'profession_situation_modeCode'=> '$professions.workSituations.modeCode',
                        'profession_situation_activitySectorCode'=> '$professions.workSituations.activitySectorCode',
                        'profession_situation_pharmacistTableSectionCode'=> '$professions.workSituations.pharmacistTableSectionCode',
                        'profession_situation_roleCode'=> '$professions.workSituations.roleCode',
                        'structure_siteSIRET'=> '$thisStructure.siteSIRET',
                        'structure_siteSIREN'=> '$thisStructure.siteSIREN',
                        'structure_siteFINESS'=> '$thisStructure.siteFINESS',
                        'structure_legalEstablishmentFINESS'=> '$thisStructure.legalEstablishmentFINESS',
                        'structure_structureTechnicalId'=> '$thisStructure.structureTechnicalId',
                        'structure_legalCommercialName'=> '$thisStructure.legalCommercialName',
                        'structure_publicCommercialName'=> '$thisStructure.publicCommercialName',
                        'structure_recipientAdditionalInfo'=> '$thisStructure.recipientAdditionalInfo',
                        'structure_geoLocationAdditionalInfo'=> '$thisStructure.geoLocationAdditionalInfo',
                        'structure_streetNumber'=> '$thisStructure.streetNumber',
                        'structure_streetNumberRepetitionIndex'=> '$thisStructure.streetNumberRepetitionIndex',
                        'structure_streetCategoryCode'=> '$thisStructure.streetCategoryCode',
                        'structure_streetLabel'=> '$thisStructure.streetLabel',
                        'structure_distributionMention'=> '$thisStructure.distributionMention',
                        'structure_cedexOffice'=> '$thisStructure.cedexOffice',
                        'structure_postalCode'=> '$thisStructure.postalCode',
                        'structure_communeCode'=> '$thisStructure.communeCode',
                        'structure_countryCode'=> '$thisStructure.countryCode',
                        'structure_phone'=> '$thisStructure.phone',
                        'structure_phone2'=> '$thisStructure.phone2',
                        'structure_fax'=> '$thisStructure.fax',
                        'structure_email'=> '$thisStructure.email',
                        'structure_departmentCode'=> '$thisStructure.departmentCode',
                        'structure_oldStructureId'=> '$thisStructure.oldStructureId',
                        'structure_registrationAuthority'=> '$thisStructure.registrationAuthority'
                    ],
                ],
                [ '$out' => 'extractRass'],
            ]);
        });
    }
}
