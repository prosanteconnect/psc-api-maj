<?php

namespace App\Http\Controllers\Api;

class WorkSituationController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @param $psId
     * @param $exProId
     * @return mixed
     */
    public function index($psId, $exProId)
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        return $this->successResponse($this->situationTransformer->transformCollection(
            $profession->workSituations()->toArray()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $psId
     * @param $exProId
     * @return mixed
     */
    public function store($psId, $exProId)
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        $situation = array_filter(request()->all());
        $situation['situId'] = $situation['roleCode'].$situation['modeCode'];

        $profession->workSituations()->create($situation);
        return $this->successResponse(null, "Creation de la situation d'exercise avec succès.");
    }

    /**
     * Display the specified resource.
     *
     * @param $psId
     * @param $exProId
     * @param $situId
     * @return mixed
     */
    public function show($psId, $exProId, $situId)
    {
        $situation = $this->getSituationOrFail($psId, $exProId, $situId);
        return $this->successResponse($this->situationTransformer->transform($situation->toArray()));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $psId
     * @param $exProId
     * @param $situId
     * @return mixed
     */
    public function update($psId, $exProId, $situId)
    {
        $situation = $this->getSituationOrFail($psId, $exProId, $situId);
        $updatedSituation = array_filter(request()->all());

        $situation->update($updatedSituation, ['upsert' => false]);
        return $this->successResponse(null, "Mise à jour de la situation d'exercise avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $psId
     * @param $exProId
     * @param $situId
     * @return mixed
     */
    public function destroy($psId, $exProId, $situId)
    {
        $situation = $this->getSituationOrFail($psId, $exProId, $situId);

        $situation->delete();
        return $this->successResponse(null, "Suppression de la situation d'exercise avec succès.");
    }
}
