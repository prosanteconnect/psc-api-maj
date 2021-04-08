<?php


namespace App\Psc\Transformers;


/**
 * Class WorkSituationTransformer
 * @package App\Psc\Transformers
 */
class WorkSituationTransformer extends Transformer {

    /**
     * transform Expertise into a protected Expertise.
     *
     * @param $situation
     * @return mixed
     */
    public function transform($situation)
    {
        return $situation;
    }
}
