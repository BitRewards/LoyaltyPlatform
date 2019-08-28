<?php

namespace App\Traits;

use App\Crud\CrudController;

/**
 * Trait ExtraFields.
 *
 * @mixin CrudController
 */
trait ExtraFields
{
    public function removeFromSource($entry, $source, $name)
    {
        $entrySource = $entry->{$source} ?? null;

        if ($entrySource && is_string($entrySource)) {
            $entrySource = json_decode($entrySource, true);
        }

        unset($entrySource[$name]);
        $entry->{$source} = json_encode($entrySource);
    }

    /**
     * @param string $container
     * @param array  $fields
     */
    public function addExtraFields(string $source, array $fields = [], $onlyIfExists = true)
    {
        $entry = $this->crud->getCurrentEntry();

        foreach ($fields as $name => $data) {
            if (!$onlyIfExists || isset($entry->{$name})) {
                $this->crud->addField(
                    array_merge([
                        'name' => $name,
                        'fake' => true,
                        'extra_store_in' => $source,
                    ], $data)
                );

                $this->removeFromSource($entry, $source, $name);
            }
        }
    }
}
