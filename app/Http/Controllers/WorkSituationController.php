<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;

class WorkSituationController extends ApiController
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
            $this->situationRules(''),
            $this->structureRefRules('structures.*.')
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param $psId
     * @param $exProId
     * @return JsonResponse
     */
    public function index($psId, $exProId): JsonResponse
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
     * @return JsonResponse
     */
    public function store($psId, $exProId): JsonResponse
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        $situation = $this->validateSituation();

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
     * @return JsonResponse
     */
    public function show($psId, $exProId, $situId): JsonResponse
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
     * @return JsonResponse
     */
    public function update($psId, $exProId, $situId): JsonResponse
    {
        $situation = $this->getSituationOrFail($psId, $exProId, $situId);
        $situation->update($this->validateSituation(), ['upsert' => false]);
        return $this->successResponse($this->printId($psId, $exProId, $situId),
            "Mise à jour de la situation d'exercise avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $psId
     * @param $exProId
     * @param $situId
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($psId, $exProId, $situId): JsonResponse
    {
        $situation = $this->getSituationOrFail($psId, $exProId, $situId);

        $situation->delete();
        return $this->successResponse($this->printId($psId, $exProId, $situId),
            "Suppression de la situation d'exercise avec succès.");
    }

    private function validateSituation(): array
    {
        $validator = Validator::make(request()->all(), $this->rules, $this->getCustomMessages());

        $situation = $validator->validate();
        $situation['situId'] = $this->getSituationCompositeId($situation);
        return $situation;
    }

    #[ArrayShape(['nationalId' => "string", 'exProId' => "", 'situId' => ""])]
    private function printId($psId, $exProId, $situId): array
    {
        return array('nationalId'=>urldecode($psId), 'exProId'=>$exProId, 'situId'=>$situId);
    }
}
