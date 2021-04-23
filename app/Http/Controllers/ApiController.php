<?php


namespace App\Http\Controllers;


use App\Models\Ps;
use App\Models\Structure;
use App\Psc\Transformers\ExpertiseTransformer;
use App\Psc\Transformers\ProfessionTransformer;
use App\Psc\Transformers\PsTransformer;
use App\Psc\Transformers\StructureTransformer;
use App\Psc\Transformers\WorkSituationTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ApiController
 * @package App\Http\Controllers\Api
 */
class ApiController extends Controller
{
    use ApiResponder;

    /**
     * @var PsTransformer
     */
    protected $psTransformer;
    /**
     * @var ProfessionTransformer
     */
    protected $professionTransformer;
    /**
     * @var ExpertiseTransformer
     */
    protected $expertiseTransformer;
    /**
     * @var WorkSituationTransformer
     */
    protected $situationTransformer;

    /**
     * @var StructureTransformer
     */
    protected $structureTransformer;

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

    protected function getNested(array $parent, String $child): array
    {
        $nested = isset($parent[$child]) ? $parent[$child] : null;
        unset($parent[$child]);
        return ['itself' => $parent, $child => $nested];
    }

    /**
     * @param $psId
     * @return bool
     */
    protected function isNewPs($psId) : bool
    {
        $ps = Ps::find(urldecode($psId));

        if ($ps) {
            $this->alreadyExistsResponse("Ce professionel existe déjà.")->send();
            die();
        }

        return true;
    }

    /**
     * @param $psId
     * @return Ps
     */
    protected function getPsOrFail($psId) : Ps
    {
        try {
            $ps = Ps::findOrFail(urldecode($psId));
        } catch(ModelNotFoundException $e) {
            $this->notFoundResponse("Ce professionel n'exist pas.")->send();
            die();
        }
        return $ps;
    }

    /**
     * @param $structureId
     * @return Structure
     */
    protected function getStructureOrFail($structureId) : Structure
    {
        try {
            $structure = Structure::findOrFail($structureId);
        } catch(ModelNotFoundException $e) {
            $this->notFoundResponse("Cette structure n'existe pas.")->send();
            die();
        }
        return $structure;
    }

    /**
     * @param $psId
     * @param $exProId
     * @return mixed
     */
    protected function getExProOrFail($psId, $exProId)
    {
        $ps = $this->getPsOrFail($psId);
        $profession = $ps->professions()->firstWhere('exProId', $exProId);
        if (! $profession) {
            $this->notFoundResponse("Cet exercice professionnel n'exist pas.")->send();
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
    protected function getExpertiseOrFail($psId, $exProId, $expertiseId) {
        $profession = $this->getExProOrFail($psId, $exProId);
        $expertise = $profession->expertises()->firstWhere('expertiseId', $expertiseId);
        if (! $expertise) {
            $this->notFoundResponse("Ce savoir fair n'exist pas.")->send();
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
    protected function getSituationOrFail($psId, $exProId, $situId) {
        $profession = $this->getExProOrFail($psId, $exProId);
        $situation = $profession->workSituations()->firstWhere('situId', $situId);
        if (! $situation) {
            $this->notFoundResponse("Cette situation d'exercise n'exist pas.")->send();
            die();
        }
        return $situation;
    }
}
