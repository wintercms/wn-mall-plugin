<?php

namespace Winter\Mall\Classes\Traits\Category;

use Cache;
use Winter\Mall\Models\Category;
use System\Classes\PluginManager;

trait Translation
{

    /**
     * Translate url parameters when the user switches the active locale.
     *
     * @param $params
     * @param $oldLocale
     * @param $newLocale
     *
     * @return mixed
     */
    public static function translateParams($params, $oldLocale, $newLocale)
    {
        $newParams = $params;
        foreach ($params as $paramName => $paramValue) {
            $records = self::transWhere($paramName, $paramValue, $oldLocale)->first();
            if ($records) {
                $records->setTranslateContext($newLocale);
                $newParams[$paramName] = $records->$paramName;
            } elseif ($paramName === 'slug') {
                // Translate nested slugs.
                $model    = new Category;
                $category = array_get($model->getSlugMap($oldLocale), $newParams['slug'] ?? -1);
                if ( ! $category) {
                    continue;
                }

                $translationMap = array_flip($model->getSlugMap($newLocale));
                $translatedSlug = array_get($translationMap, $category);
                if ($translatedSlug) {
                    $newParams['slug'] = $translatedSlug;
                }
            }
        }

        return $newParams;
    }

    /**
     * Conditionally set the translate context.
     *
     * @param string $locale
     */
    public function setTranslateContext(string $locale)
    {
        if ($this->rainlabTranslateInstalled()) {
            $this->translateContext($locale);
        }
    }

    /**
     * Conditionally gets the translate context.
     */
    public function getTranslateContext()
    {
        if ($this->rainlabTranslateInstalled()) {
            return $this->translateContext();
        }

        return Category::DEFAULT_LOCALE;
    }

    /**
     * Returns the currently active locale.
     *
     * @param $locale
     *
     * @return string
     */
    protected function getLocale($locale = null): string
    {
        if ($locale !== null) {
            return $locale;
        }

        $locale = Category::DEFAULT_LOCALE;
        if (class_exists(\Winter\Translate\Classes\Translator::class)) {
            $locale = \Winter\Translate\Classes\Translator::instance()->getLocale();
        }

        return $locale;
    }

    /**
     * Returns an array of all available locales.
     *
     * @return array
     */
    protected function getLocales(): array
    {
        $locales = [Category::DEFAULT_LOCALE];
        if ($this->rainlabTranslateInstalled()) {
            $locales = \Winter\Translate\Models\Locale::get(['code'])->pluck('code')->toArray();
        }

        return $locales;
    }

    /**
     * Check if the Translator class of Winter.Translate is available.
     *
     * @return bool
     */
    protected function rainlabTranslateInstalled(): bool
    {
        return PluginManager::instance()->exists('Winter.Translate');
    }
}
