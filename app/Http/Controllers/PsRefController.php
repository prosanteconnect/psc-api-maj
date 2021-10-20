<?php


namespace App\Http\Controllers;


use App\Models\PsRef;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PsRefController extends ApiController
{
    /**
     * @var array
     */
    private array $rules;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->rules = array(
            'nationalIdRef' => 'required',
            'nationalId'    => 'required',
            'activated'     => 'nullable|integer|numeric',
            'deactivated'   => 'nullable|integer|numeric'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $validPsRef = $this->validatePsRef();
        $psRefId = $validPsRef['nationalIdRef'];
        $nationalId = $validPsRef['nationalId'];
        if (!$this->isNewPsRef($psRefId)) {
            return $this->alreadyExistsResponse("Ce Lien existe déjà.",$this->printId($psRefId, $nationalId));
        }
        if (!isset($validPsRef['activated']) && !isset($validPsRef['deactivated'])) {
            $validPsRef['activated'] = Carbon::now()->timestamp;
        }
        PsRef::query()->create($validPsRef);
        return $this->successResponse($this->printId($psRefId, $nationalId),
            'Creation du Lien vers le Ps avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $psRefId
     * @return JsonResponse
     */
    public function destroy($psRefId): JsonResponse
    {
        $psRef = PsRef::query()->findOrFail(urldecode($psRefId));
        $psRef->delete();
        return $this->successResponse($this->printId($psRefId, $psRef->nationalId), 'Supression du Lien avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param $psRefId
     * @return JsonResponse
     */
    public function show($psRefId): JsonResponse
    {
        $psRef = PsRef::query()->findOrFail(urldecode($psRefId));
        return $this->successResponse($psRef);
    }

    public function showAll(): JsonResponse
    {
        PsRef::chunk(10000,function($psRefs) {
            foreach($psRefs as $psRef) {
                $storedPsRefs[] = $psRef 
            }
        }
        return $this->successResponse($storedPsRefs);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $psRefId
     * @return JsonResponse
     */
    public function update($psRefId): JsonResponse
    {
        $psRef = PsRef::query()->findOrFail(urldecode($psRefId));
        $psRef->update($this->validatePsRef());
        return $this->successResponse($psRef, 'Mise à jour du Lien avec succès.');
    }

    private function validatePsRef(): array
    {
        $psRefData = request()->all();
        $validator = Validator::make($psRefData, $this->rules, $this->getCustomMessages());

        // don't try-catch this block
        // we need it to throw a validation exception that will be handled by handler.
        return $validator->validate();
    }

    private function printId($psRefId, $nationalId): array
    {
        return array('nationalIdRef'=>urldecode($psRefId), 'nationalId'=>urldecode($nationalId));
    }

    /**
     * @param $id
     * @return bool
     */
    private function isNewPsRef($id) : bool
    {
        $psRef = PsRef::query()->find($id);
        if ($psRef) {
            return false;
        }
        return true;
    }

    /**
     * @return string[]
     */
    protected function getCustomMessages(): array
    {
        // custom messages
        return [
            'required' => "l'attribut :attribute est obligatoire.",
            'date' => ':attribute pas une date.',
            'forbidden_attribute' => "l'attribut :attribute est illégal."
        ];
    }
}
