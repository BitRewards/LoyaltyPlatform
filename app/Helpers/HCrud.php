<?php

class HCrud
{
    public static function resource($name, $controller, array $options = [])
    {
        // CRUD routes
        Route::post($name.'/search', [
            'as' => 'crud.'.$name.'.search',
            'uses' => $controller.'@search',
        ]);
        Route::get($name.'/reorder', [
            'as' => 'crud.'.$name.'.reorder',
            'uses' => $controller.'@reorder',
        ]);
        Route::post($name.'/reorder', [
            'as' => 'crud.'.$name.'.save.reorder',
            'uses' => $controller.'@saveReorder',
        ]);
        Route::get($name.'/{id}/details', [
            'as' => 'crud.'.$name.'.showDetailsRow',
            'uses' => $controller.'@showDetailsRow',
        ]);
        Route::get($name.'/{id}/translate/{lang}', [
            'as' => 'crud.'.$name.'.translateItem',
            'uses' => $controller.'@translateItem',
        ]);
        Route::get($name.'/{id}/revisions', [
            'as' => 'crud.'.$name.'.listRevisions',
            'uses' => $controller.'@listRevisions',
        ]);
        Route::post($name.'/{id}/revisions/{revisionId}/restore', [
            'as' => 'crud.'.$name.'.restoreRevision',
            'uses' => $controller.'@restoreRevision',
        ]);

        // CRUD
        Route::get($name, [
            'uses' => $controller.'@index',
        ])->name("crud.$name.index");

        Route::get($name.'/create', [
            'uses' => $controller.'@create',
        ])->name("crud.$name.create");

        Route::post($name, [
            'uses' => $controller.'@store',
        ])->name("crud.$name.store");

        $abilityEntity = array_get($options, 'ability_entity', $name);
        $routeBinding = array_get($options, 'route_binding', $name);

        Route::get($name."/{{$routeBinding}}", [
            'uses' => $controller.'@show',
        ])->middleware("can:view,$abilityEntity")->name("crud.$name.show");

        Route::get($name."/{{$routeBinding}}/edit", [
            'uses' => $controller.'@edit',
        ])->middleware("can:update,$abilityEntity")->name("crud.$name.edit");

        Route::get($name."/{{$routeBinding}}/copy", [
            'uses' => $controller.'@copy',
        ])->middleware("can:view,$abilityEntity")->name("crud.$name.copy");

        Route::match(['put', 'patch'], $name."/{{$routeBinding}}", [
            'uses' => $controller.'@update',
        ])->middleware("can:update,$abilityEntity")->name("crud.$name.update");

        Route::delete($name."/{{$routeBinding}}", [
            'uses' => $controller.'@destroy',
        ])->middleware("can:destroy,$abilityEntity")->name("crud.$name.destory");
    }
}
