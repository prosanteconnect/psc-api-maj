<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProfessionController extends ApiController
{
    private array $rules;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->rules = array_merge(
            $this->exProRules(''),
            $this->expertiseRules('expertises.*.'),
            $this->situationRules('workSituations.*.'),
            $this->structureRefRules('workSituations.*.structures.*.')
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param $psId
     * @return mixed
     */
    public function index($psId): JsonResponse
    {
        $ps = $this->getPsOrFail($psId);
        return $this->successResponse($this->professionTransformer->transformCollection($ps->professions()->toArray()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $psId
     * @return JsonResponse
     */
    public function store($psId): JsonResponse
    {
        $ps = $this->getPsOrFail($psId);
        $profession = $this->validateProfession();
        $ps->professions()->create($profession);
        return $this->successResponse($this->printId($psId, $profession['exProId']), "Creation de l'exercice professionnel avec succès.");
    }

    /**
     * Display the specified resource.
     *
     * @param $psId
     * @param $exProId
     * @return JsonResponse
     */
    public function show($psId, $exProId): JsonResponse
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        return $this->successResponse($this->professionTransformer->transform($profession->toArray()));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $psId
     * @param $exProId
     * @return JsonResponse
     */
    public function update($psId, $exProId): JsonResponse
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        $profession->update($this->validateProfession(), ['upsert' => false]);
        return $this->successResponse($this->printId($psId, $exProId), "Mise à jour de l'exercise pro avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $psId
     * @param $exProId
     * @return JsonResponse
     */
    public function destroy($psId, $exProId): JsonResponse
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        $profession->delete();
        return $this->successResponse($this->printId($psId, $exProId), "Suppression de l'exercise pro avec succès.");
    }

    private function validateProfession(): array
    {
        $validator = Validator::make(request()->all(), $this->rules, $this->getCustomMessages());

        $profession = $validator->validate();
        $profession['exProId'] = $this->getProfessionCompositeId($profession);
        return $profession;
    }

    private function printId($psId, $exProId): array
    {
        return array('nationalId'=>urldecode($psId), 'exProId'=>$exProId);
    }

}
