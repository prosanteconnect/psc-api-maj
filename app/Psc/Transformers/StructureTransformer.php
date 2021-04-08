<?php


namespace App\Psc\Transformers;


/**
 * Class StructureTransformer
 * @package App\Psc\Transformers
 */
class StructureTransformer extends Transformer {

    /**
     * transform Structure into a protected Structure.
     *
     * @param $structure
     * @return mixed
     */
    public function transform($structure)
    {
        return $structure->toArray();
    }
}
