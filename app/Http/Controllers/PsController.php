<?php

namespace App\Http\Controllers\Api;

use App\Models\Ps;

use Exception;

class PsController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $psList = Ps::all();
        return $this->successResponse($this->psTransformer->transformCollection($psList->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        $ps = $this->validatePs();
        Ps::create($ps);

        return $this->successResponse(null, 'Creation du Ps avec succès');
    }

    /**
     * Store or Update the specified resource in storage.
     *
     * @return mixed
     */
    public function storeOrUpdate()
    {
        $id = request()->nationalId;
        $ps = Ps::find($id);

        if(!$ps) {
            return $this->store();
        }

        $psData = $this->getNested($this->validatePs(), 'professions');
        $ps->update($psData['itself']);

        foreach ($psData['professions'] as $professionData) {
            $professionData['exProId'] = $professionData['code'].$professionData['categoryCode'];

            $profession = $ps->professions()->firstWhere('exProId', $professionData['exProId']);
            if (!$profession) {
                $profession = $ps->professions()->create($professionData);
            } else {
                $profession->update($professionData);
            }

            $expertises = $this->getNested($professionData, 'expertises')['expertises'];
            foreach ($expertises as $expertiseData) {
                $expertiseData['expertiseId'] = $expertiseData['code'].$expertiseData['categoryCode'];

                $expertise = $profession->expertises()->firstWhere('expertiseId', $expertiseData['expertiseId']);
                if (!$expertise) {
                    $profession->expertises()->create($expertiseData);
                } else {
                    $expertise->update($expertiseData);
                }
            }

            $situations = $this->getNested($professionData, 'workSituations')['workSituations'];
            foreach ($situations as $situationData) {
                $situationData['situId'] = $situationData['roleCode'].$situationData['modeCode'];

                $situation = $profession->workSituations()->firstWhere('situId', $situationData['situId']);
                if (!$situation) {
                    $profession->workSituations()->create($situationData);
                } else {
                    $situation->update($situationData);
                }
            }
        }

        return $this->successResponse(null, 'Mise à jour du Ps avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $ps = $this->getPsOrFail($id);
        return $this->successResponse($this->psTransformer->transform($ps));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $ps = $this->getPsOrFail($id);
        $ps->update(array_filter(request()->all()));
        return $this->successResponse(null, 'Mise à jour du Ps avec succès.');
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
        $ps = $this->getPsOrFail($id);
        $ps->delete();
        return $this->successResponse(null, 'Supression du Ps avec succès.');
    }

    private function psRules(): array
    {
        return [
            'idType' => 'nullable|string',
            'id' => 'nullable|string',
            'nationalId' => 'required|unique:ps',
            'lastName' => 'nullable|string',
            'firstName' => 'nullable|string',
            'dateOfBirth' => 'nullable|string',
            'birthAddressCode' => 'nullable|string',
            'birthCountryCode' => 'nullable|string',
            'birthAddress' => 'nullable|string',
            'genderCode' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'salutationCode' => 'nullable|string',
            'professions' => 'nullable|array'
        ];
    }

    private function exProRules(): array
    {
        return [
            'professions.*.code' => 'nullable|string',
            'professions.*.categoryCode' => 'nullable|string',
            'professions.*.salutationCode' => 'nullable|string',
            'professions.*.lastName' => 'nullable|string',
            'professions.*.firstName' => 'nullable|string',
            'professions.*.expertises' => 'nullable|array',
            'professions.*.workSituations' => 'nullable|array',
        ];
    }

    private function expertiseRules(): array
    {
        return [
            'professions.*.expertises.*.code' => 'nullable|string',
            'professions.*.expertises.*.categoryCode' => 'nullable|string',
        ];
    }

    private function situationRules(): array
    {
        return [
            'professions.*.workSituations.*.modeCode' => 'nullable|string',
            'professions.*.workSituations.*.activitySectorCode' => 'nullable|string',
            'professions.*.workSituations.*.pharmacistTableSectionCode' => 'nullable|string',
            'professions.*.workSituations.*.roleCode' => 'nullable|string'
        ];
    }

    private function validatePs(): array
    {
        $rules = array_merge($this->psRules(), $this->exProRules(), $this->expertiseRules(), $this->situationRules());
        $customMessages = [
            'required' => ':attribute est obligatoir.',
            'unique' => ':attribute existe déjà.'
        ];
        $ps = request()->validate($rules, $customMessages);
        foreach ($ps['professions'] as &$profession) {
            $profession['exProId'] = ( isset($profession['code']) ? $profession['code'] : '' )
                .( isset($profession['categoryCode']) ? $profession['categoryCode'] : '' );

            foreach ($profession['expertises'] as &$expertise) {
                $expertise['expertiseId'] = ( isset($expertise['code']) ? $expertise['code'] : '' )
                    .( isset($expertise['categoryCode']) ? $expertise['categoryCode'] : '' );
            }
            foreach ($profession['workSituations'] as &$situation) {
                $situation['situId'] = ( isset($situation['roleCode']) ? $situation['roleCode'] : '' )
                    .( isset($situation['modeCode']) ? $situation['modeCode'] : '' );
            }
        }
        return $ps;
    }

}
