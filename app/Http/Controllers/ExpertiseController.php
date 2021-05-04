<?php

namespace App\Http\Controllers;

class ExpertiseController extends ApiController
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
        return $this->successResponse($this->expertiseTransformer->transformCollection(
            $profession->expertises()->toArray()));
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
        $expertise = array_filter(request()->all());
        $expertise['expertiseId'] = ($expertise['code'] ?? '').($expertise['categoryCode'] ?? '');

        $profession->expertises()->create($expertise);
        return $this->successResponse(null, "Creation de l'expertise avec succès.");

    }

    /**
     * Display the specified resource.
     *
     * @param $psId
     * @param $exProId
     * @param $expertiseId
     * @return mixed
     */
    public function show($psId, $exProId, $expertiseId)
    {
        $expertise = $this->getExpertiseOrFail($psId, $exProId, $expertiseId);
        return $this->successResponse($this->expertiseTransformer->transform($expertise));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $psId
     * @param $exProId
     * @param $expertiseId
     * @return mixed
     */
    public function update($psId, $exProId, $expertiseId)
    {
        $expertise = $this->getExpertiseOrFail($psId, $exProId, $expertiseId);
        $updatedExpertise = array_filter(request()->all());

        $expertise->update($updatedExpertise, ['upsert' => false]);
        return $this->successResponse(null, "Mise à jour du savoir faire avec succès.");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $psId
     * @param $exProId
     * @param $expertiseId
     * @return mixed
     */
    public function destroy($psId, $exProId, $expertiseId)
    {
        $expertise = $this->getExpertiseOrFail($psId, $exProId, $expertiseId);

        $expertise->delete();
        return $this->successResponse(null, "Suppression de l'expertise avec succès.");
    }
}
