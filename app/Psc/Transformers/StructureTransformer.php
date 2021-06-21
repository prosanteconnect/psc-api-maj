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
     * @param null $id
     * @return mixed
     */
    public function transform($structure, $id=null): mixed
    {
        return $structure->toArray();
    }
}
