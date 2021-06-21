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
     * @param null $id
     * @return mixed
     */
    public function transform($expertise, $id=null): mixed
    {
        return $expertise;
    }
}
