<?php

namespace Winter\Mall\Components;

use Cms\Classes\ComponentBase;
use Winter\Mall\Classes\Traits\HashIds;

/**
 * This is the base class of all Winter.Mall components.
 */
abstract class MallComponent extends ComponentBase
{
    use HashIds;

    protected function setVar($name, $value)
    {
        return $this->$name = $this->page[$name] = $value;
    }
}