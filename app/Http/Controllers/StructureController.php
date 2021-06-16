<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JetBrains\PhpStorm\ArrayShape;

class StructureController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): JsonResponse
    {
        $structures = Structure::query()->paginate(10);
        return $this->successResponse($this->structureTransformer->transformCollection($structures->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store(): mixed
    {
        $structureId = request()->structureTechnicalId;
        if ($this->isNewStructure($structureId)) {
            Structure::query()->create($this->validateStructure());
        } else {
            return $this->alreadyExistsResponse("Cette structure existe déjà.",
                array('structureId' => urldecode($structureId)));
        }
        return $this->successResponse($this->printId($structureId), 'Creation de la structure avec succès.');
    }

    public function storeOrUpdate(): JsonResponse
    {
        $structureId = request()->structureTechnicalId;
        $validatedStructure = $this->validateStructure();
        $structure = Structure::query()->find(urldecode($structureId));

        if(!$structure) {
            try {
                Structure::query()->create($validatedStructure);
                return $this->successResponse($this->printId($structureId), 'Creation de la structure avec succès.');
            } catch (Exception) { // in case of concurrent create in DB
                $structure = Structure::query()->find(urldecode($structureId));
            }
        }

        $structure->update($validatedStructure);

        return $this->successResponse($this->printId($structureId), 'Mise à jour des données de la structure avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param $structureId
     * @return JsonResponse
     */
    public function show($structureId): JsonResponse
    {
        $structure = $this->getStructureOrFail($structureId);
        return $this->successResponse($this->structureTransformer->transform($structure));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $structureId
     * @return JsonResponse
     */
    public function update($structureId): JsonResponse
    {
        $structure = $this->getStructureOrFail($structureId);
        $structure->update(array_filter(request()->all()));
        return $this->successResponse($this->printId($structureId), 'Mise à jour de la Structure avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $structureId
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($structureId): JsonResponse
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
            'structureTechnicalId' => 'required',
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
            'required' => "l'attribut :attribute est obligatoire.",
            'unique' => ':attribute existe déjà.'
        ];

        $validator = Validator::make(request()->all(), $this->structureRules(), $customMessages);

        if ($validator->fails()) {
            $this->errorResponse($validator->errors()->first(), 500)->send();
            die();
        }

        try {
            return $validator->validate();
        } catch (ValidationException $e) {
            $this->errorResponse($e->getMessage(), 500)->send();
            die();
        }
    }

    #[ArrayShape(['structureId' => "string"])]
    private function printId($structureId): array
    {
        return array('structureId'=>urldecode($structureId));
    }

}
