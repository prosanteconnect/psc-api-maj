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
            } catch (Exception $ex) { // in case of concurrent create in DB
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
        $structure->update($this->validateStructure());
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

    private function validateStructure(): array
    {
        $validator = Validator::make(request()->all(), $this->structureRules(), $this->getCustomMessages());
        return $validator->validate();
    }

    private function printId($structureId): array
    {
        return array('structureId'=>urldecode($structureId));
    }

}
