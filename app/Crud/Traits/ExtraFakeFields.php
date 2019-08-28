<?php

namespace App\Crud\Traits;

use App\Crud\CrudPanel;

/**
 * Trait ExtraFakeFields.
 *
 * @mixin CrudPanel
 */
trait ExtraFakeFields
{
    public function compactFakeFields($requestInput, $form = 'create', $id = false)
    {
        // get the right fields according to the form type (create/update)
        $fields = $this->getFields($form, $id);

        $compactedFakeFields = [];

        foreach ($fields as $field) {
            // compact fake fields
            if (isset($field['fake']) && true == $field['fake'] && array_key_exists($field['name'], $requestInput)) {
                $fakeFieldKey = $field['extra_store_in'] ?? (isset($field['store_in']) ? $field['store_in'] : 'extras');

                if ($this->entry) {
                    $requestInput[$fakeFieldKey] = $requestInput[$fakeFieldKey] ?? $this->entry->getOriginal($fakeFieldKey) ?? null;
                }
                $this->addCompactedField($requestInput, $field['name'], $fakeFieldKey);

                if (!in_array($fakeFieldKey, $compactedFakeFields)) {
                    $compactedFakeFields[] = $fakeFieldKey;
                }
            }
        }

        // json_encode all fake_value columns if applicable in the database, so they can be properly stored and interpreted
        foreach ($compactedFakeFields as $value) {
            if (!(property_exists($this->model, 'translatable') && in_array($value, $this->model->getTranslatableAttributes(), true)) && $this->model->shouldEncodeFake($value)) {
                $requestInput[$value] = json_encode($requestInput[$value]);
            }
        }

        // if there are no fake fields defined, return the original request input
        return $requestInput;
    }

    private function addCompactedField(&$requestInput, $fakeFieldName, $fakeFieldKey)
    {
        $fakeField = $requestInput[$fakeFieldName];
        array_pull($requestInput, $fakeFieldName); // remove the fake field from the request

        if (is_object($requestInput[$fakeFieldKey])) {
            $decodedJsonData = json_decode(json_encode($requestInput[$fakeFieldKey]), true);
            $decodedJsonData[$fakeFieldName] = $fakeField;
            $requestInput[$fakeFieldKey] = json_decode(json_encode($decodedJsonData));
        } elseif (is_array($requestInput[$fakeFieldKey])) {
            $requestInput[$fakeFieldKey][$fakeFieldName] = $fakeField;
        } else {
            $requestInput[$fakeFieldKey] = json_decode($requestInput[$fakeFieldKey], true);
            $requestInput[$fakeFieldKey][$fakeFieldName] = $fakeField;
        }
    }
}
