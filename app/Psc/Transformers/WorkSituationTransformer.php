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
     * @param null $id
     * @return mixed
     */
    public function transform($situation, $id=null): mixed
    {
        return $situation;
    }
}
