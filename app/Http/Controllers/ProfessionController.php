<?php

namespace App\Http\Controllers;

class ProfessionController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @param $psId
     * @return mixed
     */
    public function index($psId)
    {
        $ps = $this->getPsOrFail($psId);
        return $this->successResponse($this->professionTransformer->transformCollection($ps->professions()->toArray()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $psId
     * @return mixed
     */
    public function store($psId)
    {
        $ps = $this->getPsOrFail($psId);
        $profession = array_filter(request()->all());
        $profession['exProId'] = ($profession['code'] ?? '').($profession['categoryCode'] ?? '');

        $ps->professions()->create($profession);
        return $this->successResponse(null, "Creation de l'exercice professionnel avec succès.");
    }

    /**
     * Display the specified resource.
     *
     * @param $psId
     * @param $exProId
     * @return mixed
     */
    public function show($psId, $exProId)
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        return $this->successResponse($this->professionTransformer->transform($profession->toArray()));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $psId
     * @param $exProId
     * @return mixed
     */
    public function update($psId, $exProId)
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        $updatedProfession = array_filter(request()->all());

        $profession->update($updatedProfession, ['upsert' => false]);
        return $this->successResponse(null, "Mise à jour de l'exercise pro avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $psId
     * @param $exProId
     * @return mixed
     */
    public function destroy($psId, $exProId)
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        $profession->delete();
        return $this->successResponse(null, "Suppression de l'exercise pro avec succès.");
    }

}
