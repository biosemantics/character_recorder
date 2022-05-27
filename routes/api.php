<?php


Route::group([
        'prefix' => '/v1',
        'namespace' => 'Api\V1',
        'middleware' => ['cors'],
        'as' => 'api.'
    ], function () {
    Route::post('log',                                  ['as' => 'log',                         'uses' => 'HomeController@log']);
    Route::get('activity_log',                          ['as' => 'activity_log',                'uses' => 'HomeController@activity_log']);
    Route::get('standard_characters',                   ['as' => 'standard_characters',         'uses' => 'HomeController@getStandardCharacters']);
    Route::get('user-tag/{userId}',                     ['as' => 'get_user_tag',                'uses' => 'HomeController@getUserTags']);
    Route::post('user-tag/create',                      ['as' => 'create_user_tag',             'uses' => 'HomeController@createUserTag']);
    Route::post('user-tag/remove',                      ['as' => 'remove_user_tag',             'uses' => 'HomeController@removeUserTag']);
    Route::post('matrix-store',                         ['as' => 'store_matrix',                'uses' => 'HomeController@storeMatrix']);
    Route::post('delete-header/{headerId}',             ['as' => 'delete_header',               'uses' => 'HomeController@deleteHeader']);
    Route::post('change-taxon/{taxon}',                 ['as' => 'change_taxon',                'uses' => 'HomeController@changeTaxon']);
    Route::post('add-more-column/{columnCount}',        ['as' => 'add_more_column',             'uses' => 'HomeController@addMoreColumn']);
    Route::post('show-tab-character/{tabName}',         ['as' => 'show_tab_character',          'uses' => 'HomeController@showTabCharacter']);
    Route::post('export-description',                   ['as' => 'export_description',          'uses' => 'HomeController@exportDescription']);
    Route::post('export-description-csv',               ['as' => 'export_description_csv',      'uses' => 'HomeController@exportDescriptionCsv']);
    Route::post('export-description-trig',              ['as' => 'export_description_trig',     'uses' => 'HomeController@exportDescriptionTrig']);
    Route::post('update-header',                        ['as' => 'update_header',               'uses' => 'HomeController@updateHeader']);
    Route::get('get-usage/{characterId}',               ['as' => 'get_usage',                   'uses' => 'HomeController@getUsage']);
    Route::get('get-color-details/{valueId}',           ['as' => 'get_color_details',           'uses' => 'HomeController@getColorDetails']);
    Route::post('save-color-value',                     ['as' => 'save_color_value',            'uses' => 'HomeController@saveColorValue']);
    Route::post('remove-color-value',                   ['as' => 'remove_color_value',          'uses' => 'HomeController@removeColorValue']);
    Route::post('save-non-color-value',                 ['as' => 'save_non_color_value',        'uses' => 'HomeController@saveNonColorValue']);
    Route::post('remove-non-color-value',               ['as' => 'remove_non_color_value',      'uses' => 'HomeController@removeNonColorValue']);
    Route::get('get-constraint/{characterName}',        ['as' => 'get_constraint',              'uses' => 'HomeController@getConstraint']);
    Route::get('get-non-color-details/{valueId}',       ['as' => 'get_non_color_details',       'uses' => 'HomeController@getNonColorDetails']);
    Route::post('get-color-values',                     ['as' => 'get_color_values',            'uses' => 'HomeController@getColorValues']);
    Route::post('remove-each-color-details',            ['as' => 'removeEachColorDetails',      'uses' => 'HomeController@removeEachColorDetails']);
    Route::post('remove-each-non-color-details',        ['as' => 'removeEachNonColorDetails',   'uses' => 'HomeController@removeEachNonColorDetails']);
    Route::post('overwrite-value',                      ['as' => 'overwriteValue',              'uses' => 'HomeController@overwriteValue']);
    Route::post('keep-exist-value',                     ['as' => 'keepExistValue',              'uses' => 'HomeController@keepExistValue']);
    Route::post('get-default-constraint',               ['as' => 'getDefaultConstraint',        'uses' => 'HomeController@getDefaultConstraint1']);
    Route::get('graphTest',                             ['as' => 'graphTest',                   'uses' => 'HomeController@test']);
    Route::post('setNonColorValueIRI',                  ['as' => 'setNonColorValueIRI',         'uses' => 'HomeController@setNonColorValueIRI']);
    Route::post('setColorBrightnessIRI',                ['as' => 'setColorBrightnessIRI',       'uses' => 'HomeController@setColorBrightnessIRI']);
    Route::post('setColorReflectanceIRI',               ['as' => 'setColorReflectanceIRI',      'uses' => 'HomeController@setColorReflectanceIRI']);
    Route::post('setColorSaturationIRI',                ['as' => 'setColorSaturationIRI',       'uses' => 'HomeController@setColorSaturationIRI']);
    Route::post('setColorColoredIRI',                   ['as' => 'setColorColoredIRI',          'uses' => 'HomeController@setColorColoredIRI']);
    Route::post('setColorMultiColoredIRI',              ['as' => 'setColorMultiColoredIRI',     'uses' => 'HomeController@setColorMultiColoredIRI']);
    Route::post('resolveCharacter',                     ['as' => 'resolveCharacter',            'uses' => 'HomeController@resolveCharacter']);
    Route::post('resolveNonColorValue',                 ['as' => 'resolveNonColorValue',        'uses' => 'HomeController@resolveNonColorValue']);
    Route::post('resolveColorBrightness',               ['as' => 'resolveColorBrightness',      'uses' => 'HomeController@resolveColorBrightness']);
    Route::post('resolveColorReflectance',              ['as' => 'resolveColorReflectance',     'uses' => 'HomeController@resolveColorReflectance']);
    Route::post('resolveColorSaturation',               ['as' => 'resolveColorSaturation',      'uses' => 'HomeController@resolveColorSaturation']);
    Route::post('resolveColorColored',                  ['as' => 'resolveColorColored',         'uses' => 'HomeController@resolveColorColored']);
    Route::post('resolveColorMultiColored',             ['as' => 'resolveColorMultiColored',    'uses' => 'HomeController@resolveColorMultiColored']);
    Route::post('getAllDetails',                        ['as' => 'getAllDetails',               'uses' => 'HomeController@getAllDetails']);
    Route::post('nameMatrix',                           ['as' => 'nameMatrix',                  'uses' => 'HomeController@nameMatrix']);
    Route::post('importMatrix',                         ['as' => 'importMatrix',                'uses' => 'HomeController@importMatrix']);
    Route::post('overwriteMatrix',                      ['as' => 'overwriteMatrix',             'uses' => 'HomeController@overwriteMatrix']);
    Route::get('getMatrixNames',                        ['as' => 'getMatrixNames',              'uses' => 'HomeController@getMatrixNames']);
    Route::get('getTaxons',                             ['as' => 'getTaxons',                   'uses' => 'HomeController@getTaxons']);
    Route::get('resetSystem',                           ['as' => 'resetSystem',                 'uses' => 'HomeController@resetSystem']);
    Route::get('updateStandardCharacter',               ['as' => 'updateStandardCharacter',     'uses' => 'HomeController@updateStandardCharacter']);
    Route::get('newMatrix',                             ['as' => 'newMatrix',                   'uses' => 'HomeController@newMatrix']);
    Route::get('getStandardTags',                       ['as' => 'getStandardTags',             'uses' => 'HomeController@getStandardTags']);
    Route::get('getUsers',                              ['as' => 'getUsers',                    'uses' => 'HomeController@getUsers']);

    Route::group([
        'prefix' => '/character',
        'as' => 'character.'
    ], function () {
        Route::post('create',                           ['as' => 'create_character',            'uses' => 'HomeController@storeCharacter']);
        Route::post('add-character',                    ['as' => 'add_character',               'uses' => 'HomeController@addCharacter']);
        Route::post('update',                           ['as' => 'update_value',                'uses' => 'HomeController@updateValue']);
        Route::post('update-character',                 ['as' => 'update_character',            'uses' => 'HomeController@updateCharacter']);
        Route::post('update-unit',                      ['as' => 'update_unit',                 'uses' => 'HomeController@updateUnit']);
        Route::post('update-summary',                   ['as' => 'update_summary',              'uses' => 'HomeController@updateSummary']);
        Route::post('delete/{userId}/{characterId}',    ['as' => 'delete_character',            'uses' => 'HomeController@deleteCharacter']);
        Route::post('multiple_delete',    ['as' => 'multiple_delete', 'uses' => 'HomeController@multipleDeleteCharacter']);
        Route::get('search_character',    ['as' => 'search_character', 'uses' => 'HomeController@searchCharacter']);
        Route::post('add-standard',                     ['as' => 'add_standard_character',      'uses' => 'HomeController@addStandardCharacter']);
        Route::get('remove-all-standard',               ['as' => 'remove_all_standard',         'uses' => 'HomeController@removeAllStandard']);
        Route::post('remove-all',                       ['as' => 'remove_all',                  'uses' => 'HomeController@removeAll']);
        Route::get('setIRI',                            ['as' => 'set_iri',                     'uses' => 'HomeController@setIRI']);
        Route::get('setCharacterIRI',                   ['as' => 'set_character_iri',           'uses' => 'HomeController@setCharacterIRI']);
        Route::get('getCharacterNames',                 ['as' => 'get_character_names',         'uses' => 'HomeController@getCharacterNames']);
        
        Route::get('usage/{characterId}',               ['as' => 'usage',                       'uses' => 'HomeController@usage']);
        Route::post('delete-header/{headerId}',         ['as' => 'delete-header',               'uses' => 'HomeController@deleteHeader']);
        Route::post('change-order',                     ['as' => 'change-order',                'uses' => 'HomeController@changeOrder']);
        Route::get('{userId}',                          ['as' => 'get_character',               'uses' => 'HomeController@getCharacter']);
    });
});
