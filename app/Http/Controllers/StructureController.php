<?php

namespace App\Http\Controllers\Api;

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
        $structures = Structure::all();
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
        return $this->successResponse(null, 'Creation avec succès');
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $structure = $this->getStructureOrFail($id);
        return $this->successResponse($this->structureTransformer->transform($structure));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $structure = $this->getStructureOrFail($id);
        $structure->update(array_filter(request()->all()));
        return $this->successResponse(null, 'Mise à jour de la Structure avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function destroy($id)
    {
        $structure = $this->getStructureOrFail($id);
        $structure->delete();
        return $this->successResponse(null, 'Supression de la Structure avec succès.');
    }

}
