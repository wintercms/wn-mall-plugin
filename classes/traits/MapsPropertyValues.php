<?php

namespace Winter\Mall\Classes\Traits;

use Illuminate\Support\Collection;
use Winter\Mall\Models\PropertyValue;

trait MapsPropertyValues
{
    /**
     * Maps property values to the tokens used by the index.
     *
     * A checkboxlist value is stored as a JSON array, so every selected option
     * becomes its own token. The raw value is used instead of the stored
     * index_value, so records created before this change are indexed correctly
     * after running `mall:reindex` as well.
     *
     * @param Collection|null $input
     *
     * @return Collection
     */
    protected function mapProps(?Collection $input): Collection
    {
        if ($input === null) {
            return collect();
        }

        return $input->groupBy('property_id')->map(function (Collection $values) {
            $isCheckboxList = $values->first()->isCheckboxList();

            $tokens = $isCheckboxList
                ? $values->flatMap(function (PropertyValue $value) {
                    $decoded = json_decode($value->attributes['value'] ?? '', true);

                    return is_array($decoded) ? $decoded : [];
                })->map(function ($item) {
                    return str_slug((string) $item);
                })
                : $values->pluck('index_value');

            // Use an explicit check instead of a parameterless filter(), which
            // would drop the valid option value "0".
            return $tokens
                ->map(function ($token) {
                    return (string) $token;
                })
                ->filter(function ($token) {
                    return $token !== '';
                })
                ->unique()
                ->values();
        })->filter();
    }
}
