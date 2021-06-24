<?php


namespace App\Http\Controllers;


use App\Models\Expertise;
use App\Models\Profession;
use App\Models\Ps;
use App\Models\PsRef;
use App\Models\Structure;
use App\Models\WorkSituation;
use App\Psc\Transformers\ExpertiseTransformer;
use App\Psc\Transformers\ProfessionTransformer;
use App\Psc\Transformers\PsTransformer;
use App\Psc\Transformers\StructureTransformer;
use App\Psc\Transformers\WorkSituationTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class ApiController
 * @package App\Http\Controllers\Api
 */
class ApiController extends BaseController
{
    use ApiResponder;

    /**
     * @var PsTransformer
     */
    protected PsTransformer $psTransformer;
    /**
     * @var ProfessionTransformer
     */
    protected ProfessionTransformer $professionTransformer;
    /**
     * @var ExpertiseTransformer
     */
    protected ExpertiseTransformer $expertiseTransformer;
    /**
     * @var WorkSituationTransformer
     */
    protected WorkSituationTransformer $situationTransformer;

    /**
     * @var StructureTransformer
     */
    protected StructureTransformer $structureTransformer;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->psTransformer = new PsTransformer();
        $this->professionTransformer = new ProfessionTransformer();
        $this->expertiseTransformer = new ExpertiseTransformer();
        $this->situationTransformer = new WorkSituationTransformer();
        $this->structureTransformer = new StructureTransformer();
    }

    /**
     * @param array $parent
     * @param String $child
     * @return array
     */
    protected function getNested(array $parent, String $child): array
    {
        $nested = isset($parent[$child]) ? $parent[$child] : null;
        unset($parent[$child]);
        return ['itself' => $parent, $child => $nested];
    }

    /**
     * @param $id
     * @return bool
     */
    protected function isNewPs($id) : bool
    {
        $psRefId = urldecode($id);
        $psRef = PsRef::query()->find($psRefId);
        if ($psRef) {
            $psId = $psRef['nationalId'];
            $ps = Ps::query()->find($psId);
            // there's reference to a Ps
            if ($ps) return false;
            // there's reference to nowhere. Delete link. Return true
            Log::info('There is a reference to '.$psId.' but Ps does not exist. deleting link');
            $psRef->delete();
        } else {
            $ps = Ps::query()->find($psRefId);
            if ($ps) {
                // no reference but Ps exists. Create link
                Log::info('No reference to '.$psRefId.' but Ps exists. Creating link');
                PsRef::query()->create($this->psLink($psRefId));
                return false;
            }
            // no reference, no Ps. Return true
        }
        return true;
    }

    /**
     * @param $structureId
     * @return bool
     */
    protected function isNewStructure($structureId) : bool
    {
        $structure = Structure::query()->find(urldecode($structureId));
        if ($structure) false;
        return true;
    }

    /**
     * @param $psId
     * @return Ps
     */
    protected function getPsOrFail($psId): Ps
    {
        try {
            $psRef = PsRef::query()->findOrFail(urldecode($psId));
            $ps = Ps::query()->findOrFail($psRef['nationalId']);
        } catch(ModelNotFoundException) {
            $this->notFoundResponse("Ce professionel n'exist pas.",
                array('nationalId' => urldecode($psId)))->send();
            die();
        }
        return $ps;
    }

    /**
     * @param $structureId
     * @return Structure
     */
    protected function getStructureOrFail($structureId): Structure
    {
        try {
            $structure = Structure::query()->findOrFail($structureId);
        } catch(ModelNotFoundException) {
            $this->notFoundResponse("Cette structure n'existe pas.",
                array('structureId' => $structureId))->send();
            die();
        }
        return $structure;
    }

    /**
     * @param $psId
     * @param $exProId
     * @return mixed
     */
    protected function getExProOrFail($psId, $exProId) : Profession
    {
        $ps = $this->getPsOrFail($psId);
        $profession = $ps->professions()->firstWhere('exProId', $exProId);
        if (! $profession) {
            $this->notFoundResponse("Cet exercice professionnel n'exist pas.",
                array('nationalId' => urldecode($psId), 'exProId' => $exProId))->send();
            die();
        }
        return $profession;
    }

    /**
     * @param $psId
     * @param $exProId
     * @param $expertiseId
     * @return mixed
     */
    protected function getExpertiseOrFail($psId, $exProId, $expertiseId) : Expertise
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        $expertise = $profession->expertises()->firstWhere('expertiseId', $expertiseId);
        if (! $expertise) {
            $this->notFoundResponse("Ce savoir fair n'exist pas.",
                array('nationalId' => urldecode($psId), 'exProId' => $exProId, 'expertiseId' => $expertiseId))->send();
            die();
        }
        return $expertise;
    }

    /**
     * @param $psId
     * @param $exProId
     * @param $situId
     * @return mixed
     */
    protected function getSituationOrFail($psId, $exProId, $situId) : WorkSituation
    {
        $profession = $this->getExProOrFail($psId, $exProId);
        $situation = $profession->workSituations()->firstWhere('situId', $situId);
        if (! $situation) {
            $this->notFoundResponse("Cette situation d'exercise n'exist pas.",
                array('nationalId' => urldecode($psId), 'exProId' => $exProId, 'situId' => $situId))->send();
            die();
        }
        return $situation;
    }

    /**
     * @param $psId
     * @return array
     */
    #[ArrayShape(['nationalIdRef' => "string", 'nationalId' => "string", 'activated' => "float|int|string"])]
    protected function psLink($psId): array
    {
        return [
            'nationalIdRef' => urldecode($psId),
            'nationalId' => urldecode($psId),
            'activated' => Carbon::now()->timestamp
        ];
    }

    /**
     * @param $psRef
     * @return bool
     */
    protected function isActive($psRef): bool
    {
        return ($psRef->activated - $psRef->deactivated) >= 0;
    }

    /**
     * @param $profession
     * @return string
     */
    protected function getProfessionCompositeId($profession): string
    {
        $exProId = ($profession['code'] ?? '')
            .($profession['categoryCode'] ?? '');
        if ($exProId == '') {
            return 'ND';
        }
        return $exProId;
    }

    /**
     * @param $expertise
     * @return string
     */
    protected function getExpertiseCompositeId($expertise): string
    {
        $expertiseId = ($expertise['typeCode'] ?? '')
            .($expertise['code'] ?? '');
        if ($expertiseId == '') {
            return 'ND';
        }
        return $expertiseId;
    }

    /**
     * @param $situation
     * @return string
     */
    protected function getSituationCompositeId($situation): string
    {
        $situId = ($situation['modeCode'] ?? '')
            .($situation['activitySectorCode'] ?? '')
            .($situation['pharmacistTableSectionCode'] ?? '')
            .($situation['roleCode'] ?? '');
        if ($situId == '') {
            return 'ND';
        }
        return $situId;
    }

    /**
     * @param $psId
     * @param $validPs
     * @return bool
     */
    protected function savePs($psId, $validPs): bool
    {
        if ($this->isNewPs($psId)) {
            // Entry is new. Create Ps and Link
            Ps::query()->create($validPs);
            PsRef::query()->create($this->psLink($psId));
        } else {
            $psRef = PsRef::query()->findOrFail(urldecode($psId));
            if ($this->isActive($psRef)) {
                // Ps is still active. Return error.
                return false;
            } else {
                // Ps was deactivated. Change link to active and update Ps data.
                $psRef->update(['activated' => Carbon::now()->timestamp]);
                $ps = Ps::query()->findOrFail($psRef['nationalId']);
                $updatedPs = array_replace($validPs, ['nationalId' => $psRef['nationalId']]);
                $ps->update($updatedPs);
            }
        }
        return true;
    }

    /**
     * @return string[]
     */
    protected function getCustomMessages(): array
    {
        return [
            'required' => "l'attribut :attribute est obligatoire.",
            'unique' => ':attribute existe déjà.',
            'forbidden_attribute' => "l'attribut :attribute est illégal."
        ];
    }

    /**
     * @return string[]
     */
    protected function psRules(): array
    {
        return [
            'idType' => 'nullable|string',
            'id' => 'nullable|string',
            'nationalId' => 'required', //'required|unique:ps',
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

    /**
     * @param String $nested
     * @return string[]
     */
    protected function exProRules(String $nested = 'professions.*.'): array
    {
        return [
            $nested.'code' => 'nullable|string',
            $nested.'categoryCode' => 'nullable|string',
            $nested.'salutationCode' => 'nullable|string',
            $nested.'lastName' => 'nullable|string',
            $nested.'firstName' => 'nullable|string',
            $nested.'expertises' => 'nullable|array',
            $nested.'workSituations' => 'nullable|array',
        ];
    }

    /**
     * @param string $nested
     * @return string[]
     */
    protected function expertiseRules(String $nested = 'professions.*.expertises.*.'): array
    {
        return [
            $nested.'typeCode' => 'nullable|string',
            $nested.'code' => 'nullable|string',
        ];
    }

    /**
     * @param string $nested
     * @return string[]
     */
    protected function situationRules(String $nested = 'professions.*.workSituations.*.'): array
    {
        return [
            $nested.'modeCode' => 'nullable|string',
            $nested.'activitySectorCode' => 'nullable|string',
            $nested.'pharmacistTableSectionCode' => 'nullable|string',
            $nested.'roleCode' => 'nullable|string',
            $nested.'structures' => 'nullable|array'
        ];
    }

    /**
     * @param string $nested
     * @return string[]
     */
    protected function structureRefRules(String $nested = 'professions.*.workSituations.*.structures.*.'): array
    {
        return [
            $nested.'structureId' => 'nullable|String'
        ];
    }

    /**
     * @return string[]
     */
    protected function structureRules(): array
    {
        return [
            'siteSIRET' => 'nullable|string',
            'siteSIREN' => 'nullable|string',
            'siteFINESS' => 'nullable|string',
            'legalEstablishmentFINESS' => 'nullable|string',
            'structureTechnicalId' => 'required',
            'legalCommercialName' => 'nullable|string', # raison sociale site
            'publicCommercialName' => 'nullable|string', # enseigne commerciale site
            'recipientAdditionalInfo' => 'nullable|string', # Complément destinataire
            'geoLocationAdditionalInfo' => 'nullable|string', # Complément point géographique
            'streetNumber' => 'nullable|string', # Numéro Voie
            'streetNumberRepetitionIndex' => 'nullable|string', # Indice répétition voie
            'streetCategoryCode' => 'nullable|string', # Code type de voie
            'streetLabel' => 'nullable|string', # Libellé Voie
            'distributionMention' => 'nullable|string', # Mention distribution
            'cedexOffice' => 'nullable|string',
            'postalCode' => 'nullable|string',
            'communeCode' => 'nullable|string',
            'countryCode' => 'nullable|string',
            'phone' => 'nullable|string',
            'phone2' => 'nullable|string',
            'fax' => 'nullable|string',
            'email' => 'nullable|string',
            'departmentCode' => 'nullable|string',
            'oldStructureId' => 'nullable|string',
            'registrationAuthority' => 'nullable|string'
        ];
    }
}
