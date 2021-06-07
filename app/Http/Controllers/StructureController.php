<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class StructureController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): JsonResponse
    {
        $structures = Structure::paginate(10);
        return $this->successResponse($this->structureTransformer->transformCollection($structures->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        $structureId = request()->structureTechnicalId;
        if ($this->isNewPs($structureId)) {
            Structure::create($this->validateStructure());
        }
        return $this->successResponse($this->printId($structureId), 'Creation de la structure avec succès.');
    }

    public function storeOrUpdate(): JsonResponse
    {
        $structureId = request()->structureTechnicalId;
        $validatedStructure = $this->validateStructure();
        $structure = Structure::find(urldecode($structureId));

        if(!$structure) {
            try {
                Structure::create($validatedStructure);
                return $this->successResponse($this->printId($structureId), 'Creation de la structure avec succès.');
            } catch (Exception $e) { // in case of concurrent create in DB
                $structure = Structure::find(urldecode($structureId));
            }
        }

        $structure->update($validatedStructure);

        return $this->successResponse($this->printId($structureId), 'Mise à jour des données de la structure avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param $structureId
     * @return mixed
     */
    public function show($structureId)
    {
        $structure = $this->getStructureOrFail($structureId);
        return $this->successResponse($this->structureTransformer->transform($structure));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $structureId
     * @return mixed
     */
    public function update($structureId)
    {
        $structure = $this->getStructureOrFail($structureId);
        $structure->update(array_filter(request()->all()));
        return $this->successResponse($this->printId($structureId), 'Mise à jour de la Structure avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $structureId
     * @return mixed
     * @throws Exception
     */
    public function destroy($structureId)
    {
        $structure = $this->getStructureOrFail($structureId);
        $structure->delete();
        return $this->successResponse($this->printId($structureId), 'Supression de la Structure avec succès.');
    }

    private function structureRules(): array
    {
        return [
            'siteSIRET' => 'nullable|string',
            'siteSIREN' => 'nullable|string',
            'siteFINESS' => 'nullable|string',
            'legalEstablishmentFINESS' => 'nullable|string',
            'structureTechnicalId' => 'nullable|string',
            'legalCommercialName' => 'nullable|string', # raison sociale site
            'publicCommercialName' => 'nullable|string', # enseigne commerciale site
            'recipientAdditionalInfo' => 'nullable|string', # Complément destinataire
            'geoLocationAdditionalInfo' => 'nullable|string', # Complément point géographique
            'streetNumber' => 'nullable|string', # Numéro Voie
            'streetNumberRepetitionIndex' => 'nullable|string', # Indice répétition voie
            'streetCategoryCode' => 'nullable|string', # Code type de voie
            'streetLabel' => 'nullable|string', # Libellé Voie
            'distributionMention' => 'nullable|string', # Mention distribution
            'cedexOffice' => 'nullable|string',
            'postalCode' => 'nullable|string',
            'communeCode' => 'nullable|string',
            'countryCode' => 'nullable|string',
            'phone' => 'nullable|string',
            'phone2' => 'nullable|string',
            'fax' => 'nullable|string',
            'email' => 'nullable|string',
            'departmentCode' => 'nullable|string',
            'oldStructureId' => 'nullable|string',
            'registrationAuthority' => 'nullable|string'
        ];
    }

    private function validateStructure(): array
    {
        $customMessages = [
            'required' => ':attribute est obligatoire.',
            'unique' => ':attribute existe déjà.'
        ];

        $validator = Validator::make(request()->all(), $this->structureRules(), $customMessages);

        if ($validator->fails()) {
            $this->errorResponse($validator->errors()->first(), 500)->send();
            die();
        }

        return $validator->validate();
    }

    private function printId($structureId): array
    {
        return array('structureId'=>urldecode($structureId));
    }

}
