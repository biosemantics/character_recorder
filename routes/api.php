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
