<?php

namespace App\Http\Controllers;

use App\Jobs\AggregatePsJob;
use App\Models\Ps;

use App\Models\PsRef;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

class PsController extends ApiController
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
        $this->rules = array_merge(
            $this->psRules(),
            $this->exProRules(),
            $this->expertiseRules(),
            $this->situationRules(),
            $this->structureRefRules()
        );
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): JsonResponse
    {
        $psList = Ps::query()->paginate(10);
        return $this->successResponse($this->psTransformer->transformCollection($psList->all()));
    }

    /**
     * Aggregate Ps into extractRass.
     *
     */
    public function aggregate(): JsonResponse
    {
        $this->dispatch(new AggregatePsJob());
        return $this->successResponse(null, 'Aggregation initialized');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $psId = request()->nationalId;
        $validatedPs = $this->validatePs();
        if ($this->savePs($psId, $validatedPs)) {
            return $this->successResponse($this->printId($psId), 'Creation du Ps avec succès');
        } else {
            return $this->alreadyExistsResponse("Ce professionel existe déjà.",
                array('nationalId' => urldecode($psId)));
        }
    }

    /**
     * Store or Update the specified resource in storage.
     *
     * @return JsonResponse
     */
    public function storeOrUpdate(): JsonResponse
    {
        $psId = request()->nationalId;
        $validPs = $this->validatePs();
        try {
            if ($this->savePs($psId, $validPs)) {
                return $this->successResponse($this->printId($psId), 'Creation du Ps avec succès');
            } else {
                $psRef = PsRef::query()->find(urldecode($psId));
                $ps = Ps::query()->find($psRef['nationalId']);
            }
        } catch (Exception) { // in case of concurrent create in DB
            $psRef = PsRef::query()->find(urldecode($psId));
            $ps = Ps::query()->find($psRef['nationalId']);
        }

        $validPs['nationalId'] = $psRef['nationalId'];
        $psData = $this->getNested($validPs, 'professions');
        $ps->update($psData['itself']);
        $this->updateNested($ps, $psData['professions']);

        return $this->successResponse($this->printId($psId), 'Mise à jour du Ps avec succès.');
    }

    /**
     * @param $ps
     * @param $professions
     */
    protected function updateNested($ps, $professions): void
    {
        if (!isset($professions)) return;
        foreach ($professions as $professionData) {
            $professionData['exProId'] = $this->getProfessionCompositeId($professionData);
            $profession = $ps->professions()->firstWhere('exProId', $professionData['exProId']);
            if (!$profession) {
                $profession = $ps->professions()->create($professionData);
            } else {
                $profession->update($professionData);
            }

            $expertises = $this->getNested($professionData, 'expertises')['expertises'];
            $this->updateNestedExpertise($expertises, $profession);

            $situations = $this->getNested($professionData, 'workSituations')['workSituations'];
            $this->updateNestedSituation($situations, $profession);
        }
    }

    /**
     * @param mixed $expertises
     * @param $profession
     */
    protected function updateNestedExpertise(mixed $expertises, $profession): void
    {
        if (!isset($expertises)) return;
        foreach ($expertises as $expertiseData) {
            $expertiseData['expertiseId'] = $this->getExpertiseCompositeId($expertiseData);
            $expertise = $profession->expertises()->firstWhere('expertiseId', $expertiseData['expertiseId']);
            if (!$expertise) {
                $profession->expertises()->create($expertiseData);
            } else {
                $expertise->update($expertiseData);
            }
        }
    }

    /**
     * @param mixed $situations
     * @param $profession
     */
    protected function updateNestedSituation(mixed $situations, $profession): void
    {
        if (!isset($situations)) return;
        foreach ($situations as $situationData) {
            $situationData['situId'] = $this->getSituationCompositeId($situationData);
            $situation = $profession->workSituations()->firstWhere('situId', $situationData['situId']);
            if (!$situation) {
                $profession->workSituations()->create($situationData);
            } else {
                $situation->update($situationData);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $psId
     * @return JsonResponse
     */
    public function show($psId): JsonResponse
    {
        $ps = $this->getPsOrFail($psId);
        return $this->successResponse($this->psTransformer->transform($ps, urldecode($psId)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $psId
     * @return JsonResponse
     */
    public function update($psId): JsonResponse
    {
        $ps = $this->getPsOrFail($psId);
        $ps->update($this->validatePs($ps["nationalId"]));
        return $this->successResponse($this->printId($psId), 'Mise à jour du Ps avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $psId
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($psId): JsonResponse
    {
        $psRef = PsRef::query()->findOrFail(urldecode($psId));
        $psRef->update(['deactivated' => Carbon::now()->timestamp]);
        return $this->successResponse($this->printId($psId), 'Supression du Ps avec succès.');
    }

    protected function validatePs($nationalId = null): array
    {
        $psData = request()->all();
        $psData['nationalId'] = $nationalId ?? $psData['nationalId'];
        $validator = Validator::make($psData, $this->rules, $this->getCustomMessages());

        // don't try-catch this block
        // we need it to throw a validation exception that will be handled by handler.
        return $this->injectCompositeIds($validator->validate());
    }

    #[ArrayShape(['nationalId' => "string"])]
    private function printId($psId): array
    {
        return array('nationalId'=>urldecode($psId));
    }

    /**
     * @param $ps
     * @return array
     */
    protected function injectCompositeIds($ps): array
    {
        if (isset($ps['professions'])) {
            foreach ($ps['professions'] as &$profession) {
                $profession['exProId'] = $this->getProfessionCompositeId($profession);
                if (isset($profession['expertises']))
                    foreach ($profession['expertises'] as &$expertise)
                        $expertise['expertiseId'] = $this->getExpertiseCompositeId($expertise);
                if (isset($profession['workSituations']))
                    foreach ($profession['workSituations'] as &$situation)
                        $situation['situId'] = $this->getSituationCompositeId($situation);
            }
        }
        return $ps;
    }

}
