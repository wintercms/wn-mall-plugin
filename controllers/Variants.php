<?php namespace Winter\Mall\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use Winter\Mall\Models\GeneralSettings;
use Winter\Mall\Models\Property;
use Winter\Mall\Models\PropertyValue;
use Winter\Mall\Models\Variant;
use Winter\Translate\Models\Locale;

class Variants extends Controller
{
    public $implement = [
        ListController::class,
        FormController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'winter.mall.manage_products',
    ];

    public function formAfterUpdate($model)
    {
        static::handlePropertyValueUpdates($model);
    }

    public function formExtendFields($widget)
    {
        if (!GeneralSettings::get('use_orders', true)) {
            $widget->removeTab('winter.mall::lang.common.shipping');
        }
    }

    /**
     * Handle the form data form the property value form.
     */
    public static function handlePropertyValueUpdates(Variant $variant)
    {
        $locales = [];
        if (class_exists(Locale::class)) {
            $locales = Locale::isEnabled()->get();
        }

        $formData = array_wrap(post('VariantPropertyValues', []));
        if (count($formData) < 1) {
            PropertyValue::where('variant_id', $variant->id)->delete();
        }

        $properties     = Property::whereIn('id', array_keys($formData))->get();
        $propertyValues = PropertyValue::where('variant_id', $variant->id)->get();

        foreach ($formData as $id => $value) {
            $property = $properties->find($id);
            $pv       = $propertyValues->where('property_id', $id)->first()
                ?? new PropertyValue([
                    'variant_id'  => $variant->id,
                    'product_id'  => $variant->product_id,
                    'property_id' => $id,
                ]);

            $pv->value = $value;
            foreach ($locales as $locale) {
                $transValue = post(
                    sprintf('RLTranslate.%s.VariantPropertyValues.%d', $locale->code, $id),
                    post(sprintf('VariantPropertyValues.%d', $id)) // fallback
                );
                $transValue = $variant->handleTranslatedPropertyValue(
                    $property,
                    $pv,
                    $value,
                    $transValue,
                    $locale->code
                );
                $pv->setAttributeTranslated('value', $transValue, $locale->code);
            }

            if (($pv->value === null || $pv->value === '') && $pv->exists) {
                $pv->delete();
            } else {
                $pv->save();
            }
        }
    }
}
