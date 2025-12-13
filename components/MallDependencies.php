<?php namespace Winter\Mall\Components;

/**
 * The MallDependencies component bundles all needed
 * frontend assets.
 */
class MallDependencies extends MallComponent
{
    /**
     * Component details.
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'winter.mall::lang.components.dependencies.details.name',
            'description' => 'winter.mall::lang.components.dependencies.details.description',
        ];
    }

    /**
     * Properties of this component.
     *
     * @return array
     */
    public function defineProperties()
    {
        return [
        ];
    }

    /**
     * Inject frontend assets.
     *
     * @return array
     */
    public function init()
    {
        $this->addJs('assets/pubsub.js');
    }
}
