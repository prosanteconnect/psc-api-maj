<?php


namespace App\Psc\Transformers;


/**
 * Class ProfessionTransformer
 * @package App\Psc\Transformers
 */
class ProfessionTransformer extends Transformer {

    protected ExpertiseTransformer $expertiseTransformer;
    protected WorkSituationTransformer $workSituationTransformer;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->expertiseTransformer = new ExpertiseTransformer();
        $this->workSituationTransformer = new WorkSituationTransformer();
    }

    /**
     * transform Profession into a protected Profession.
     *
     * @param $profession
     * @param null $id
     * @return mixed
     */
    public function transform($profession, $id=null): mixed
    {
        $protectedProfession = $profession;
        if (isset($profession['expertises'])) {
            $protectedProfession['expertises'] = $this->expertiseTransformer->transformCollection($profession['expertises']);
        }
        if (isset($profession['situations'])) {
            $protectedProfession['situations'] = $this->workSituationTransformer->transformCollection($profession['situations']);
        }

        return $protectedProfession;
    }
}
