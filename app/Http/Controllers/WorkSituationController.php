<?php

namespace App\Http\Controllers;

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
        $situation['situId'] = $this->getSituationCompositeId($situation);

        $profession->workSituations()->create($situation);
        return $this->successResponse($this->printId($psId, $exProId, $situation['situId']),
            "Creation de la situation d'exercise avec succès.");
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
        return $this->successResponse($this->printId($psId, $exProId, $situId),
            "Mise à jour de la situation d'exercise avec succès.");
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
        return $this->successResponse($this->printId($psId, $exProId, $situId),
            "Suppression de la situation d'exercise avec succès.");
    }

    private function printId($psId, $exProId, $situId)
    {
        return array('nationalId'=>urldecode($psId), 'exProId'=>$exProId, 'situId'=>$situId);
    }
}
