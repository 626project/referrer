<?php

namespace App\Renderers;

interface RendererInterface
{
    /**
     * @param $object
     * @return array
     */
    public function toArray($object);
}
