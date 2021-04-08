<?php


namespace App\Psc\Transformers;


/**
 * Class ExpertiseTransformer
 * @package App\Psc\Transformers
 */
class ExpertiseTransformer extends Transformer {

    /**
     * transform Expertise into a protected Expertise.
     *
     * @param $expertise
     * @return mixed
     */
    public function transform($expertise)
    {
        return $expertise;
    }
}
