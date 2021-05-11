<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use Exception;

class StructureController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
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
        $structure = array_filter(request()->all());

        Structure::create($structure);
        return $this->successResponse($this->printId($structure['structureTechnicalId']), 'Creation avec succès');
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

    private function printId($structureId)
    {
        return array('structureId'=>urldecode($structureId));
    }

}
