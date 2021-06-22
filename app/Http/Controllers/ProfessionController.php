<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProfessionController extends ApiController
{

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

    private function professionRules(): array
    {
        return [
            'code' => 'nullable|string',
            'categoryCode'=> 'nullable|string',
            'salutationCode'=> 'nullable|string',
            'lastName'=> 'nullable|string',
            'firstName'=> 'nullable|string',
            'expertises'=> 'nullable|string',
            'workSituations' => 'nullable|string'
        ];
    }

    private function validateProfession(): array
    {
        $customMessages = [
            'required' => "l'attribut :attribute est obligatoire.",
            'unique' => ':attribute existe déjà.'
        ];

        $validator = Validator::make(request()->all(), $this->professionRules(), $customMessages);

        if ($validator->fails()) {
            $this->errorResponse($validator->errors()->first(), 500)->send();
            die();
        }

        try {
            $profession = $validator->validate();
            $profession['exProId'] = $this->getProfessionCompositeId($profession);
            return $profession;
        } catch (ValidationException $e) {
            $this->errorResponse($e->getMessage(), 500)->send();
            die();
        }
    }

    private function printId($psId, $exProId)
    {
        return array('nationalId'=>urldecode($psId), 'exProId'=>$exProId);
    }

}
