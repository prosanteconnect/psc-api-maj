<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;

class ExpertiseController extends ApiController
{

    private array $rules;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->rules = $this->expertiseRules('');
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
        return $this->successResponse($this->expertiseTransformer->transformCollection(
            $profession->expertises()->toArray()));
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
        $expertise = $this->validateExpertise();

        $profession->expertises()->create($expertise);
        return $this->successResponse($this->printId($psId, $exProId, $expertise['expertiseId']),
            "Creation de l'expertise avec succès.");

    }

    /**
     * Display the specified resource.
     *
     * @param $psId
     * @param $exProId
     * @param $expertiseId
     * @return JsonResponse
     */
    public function show($psId, $exProId, $expertiseId): JsonResponse
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
     * @return JsonResponse
     */
    public function update($psId, $exProId, $expertiseId): JsonResponse
    {
        $expertise = $this->getExpertiseOrFail($psId, $exProId, $expertiseId);
        $expertise->update($this->validateExpertise(), ['upsert' => false]);
        return $this->successResponse($this->printId($psId, $exProId, $expertiseId),
            "Mise à jour du savoir faire avec succès.");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $psId
     * @param $exProId
     * @param $expertiseId
     * @return JsonResponse
     */
    public function destroy($psId, $exProId, $expertiseId): JsonResponse
    {
        $expertise = $this->getExpertiseOrFail($psId, $exProId, $expertiseId);

        $expertise->delete();
        return $this->successResponse($this->printId($psId, $exProId, $expertiseId),
            "Suppression de l'expertise avec succès.");
    }

    private function validateExpertise(): array
    {
        $validator = Validator::make(request()->all(), $this->rules, $this->getCustomMessages());

        $expertise = $validator->validate();
        $expertise['expertiseId'] = $this->getExpertiseCompositeId($expertise);
        return $expertise;

    }

    #[ArrayShape(['nationalId' => "string", 'exProId' => "", 'expertiseId' => ""])]
    private function printId($psId, $exProId, $expertiseId): array
    {
        return array('nationalId'=>urldecode($psId), 'exProId'=>$exProId, 'expertiseId'=>$expertiseId);
    }
}
