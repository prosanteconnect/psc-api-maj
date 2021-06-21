<?php


namespace App\Psc\Transformers;


/**
 * Class Transformer
 * @package App\Psc\Transformers
 */
abstract class Transformer {

    /**
     * transform a collection of items.
     *
     * @param $item
     * @return array
     */
    public function transformCollection(array $item): array
    {
        return array_map([$this, 'transform'], $item);
    }

    /**
     * transform a single item.
     *
     * @param $item
     * @param null $id
     * @return mixed
     */
    public abstract function transform($item, $id=null): mixed;

}
