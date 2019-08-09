<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\StandardCharacter;
use App\Character;
use App\UserTag;
use App\User;
use App\Header;
use App\Value;
use App\ColorDetails;
use App\NonColorDetails;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only([
            'getStandardCharacters',
            'getArrayCharacters',
            'delete',
            'addHeader',
            'deleteHeader',
            'getHeaders',
            'store',
            'storeCharacter',
            'getUserTags',
            'createUserTag',
            'getCharacter',
            'removeUserTag',
            'storeMatrix',
            'deleteHeader',
            'changeTaxon',
            'addMoreColumn',
            'addCharacter',
            'updateValue',
            'addStandardCharacter',
            'removeAll',
            'removeAllStandard',
            'updateCharacter',
        ]);
    }

    public function getHeaders() {
        return Header::where('user_id', Auth::id())
            ->orWhere('user_id', NULL)
            ->orderBy('created_at', 'desc')->get();
    }

    public function getAllColorValues() {

        $colorValues = ColorDetails::all();
        $nonColorValues = NonColorDetails::all();
        
        $data = [
            'colorValues' => $colorValues,
            'nonColorValues' => $nonColorValues,
        ];

        return $data;
    }

    public function getValuesByCharacter()
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $allCharacters = Character::where('owner_name', '=', $username)->orderBy('order', 'ASC')->get();
        $headers = $this->getHeaders();
        $characters = [];
        foreach ($allCharacters as $eachCharacter) {
            $value_array = [];
            foreach ($headers as $header) {
                if ($value = Value::where(['character_id'=>$eachCharacter->id, 'header_id'=>$header->id])->first()) {

                    $currentCharacterName = Character::where('id', '=', $value->character_id)->first()->name;
                    if (substr($currentCharacterName, 0, 5) == 'Color' && $value->header_id != 1) {
                        $colorDetails = ColorDetails::where('value_id', '=', $value->id)->get();

                        if ($colorDetails->first()) {
                            foreach ($colorDetails as $eachColor) {
                                $value->value = $value->value . ($eachColor->negation ? ($eachColor->negation . ' ') : '') .
                                    ($eachColor->pre_constraint ? ($eachColor->pre_constraint . ' ') : '') .
                                    ($eachColor->brightness ? ($eachColor->brightness . ' ') : '') .
                                    ($eachColor->reflectance ? ($eachColor->reflectance . ' ') : '') .
                                    ($eachColor->saturation ? ($eachColor->saturation . ' ') : '') .
                                    ($eachColor->colored ? ($eachColor->colored . ' ') : '') .
                                    ($eachColor->multi_colored ? ($eachColor->multi_colored . ' ') : '') .
                                    ($eachColor->post_constraint ? ($eachColor->post_constraint . ' ') : '');
                            }
                            if ($value->value != '') {
                                $value->value = substr($value->value, 0, -1);
                            }
//                            $value->save();
                        }

                    } else if (!$this->checkNumericalCharacter($currentCharacterName) && $value->header_id != 1) {
                        $nonColorDetails = NonColorDetails::where('value_id', '=', $value->id)->get();

                        if ($nonColorDetails->first()) {
                            foreach ($nonColorDetails as $eachValue) {
                                $value->value = $value->value . ($eachValue->negation ? ($eachValue->negation . ' ') : '') .
                                    ($eachValue->pre_constraint ? ($eachValue->pre_constraint . ' ') : '') .
                                    ($eachValue->main_value ? ($eachValue->main_value . ' ') : '') .
                                    ($eachValue->post_constraint ? ($eachValue->post_constraint . ' ') : '');
                            }
                            if ($value->value != '') {
                                $value->value = substr($value->value, 0, -1);
                            }
                        }
                    }

                    $value->username = $eachCharacter->username;
                    $value->unit = $eachCharacter->unit;
                    $value->summary = $eachCharacter->summary;
                    $value->standard = $eachCharacter->standard;
                    array_push($value_array, $value);
                }

            }
            array_push($characters, $value_array);
        }

        return $characters;
    }

    public function checkNumericalCharacter($str) {
        if (substr($str, 0, 5) == 'Width'
            || substr($str, 0, 5) == 'Depth'
            || substr($str, 0, 5) == 'Count'
            || substr($str, 0, 8) == 'Diameter'
            || substr($str, 0, 8) == 'Distance'
            || substr($str, 0, 6) == 'Length') {
            return true;
        } else {
            return false;
        }
    }

    public function getArrayCharacters() {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $arrayCharacters = [];
        if (Character::where('owner_name', '=', $username)->first()) {
            $arrayCharacters = Character::where('owner_name', '=', $username)->orderBy('standard_tag', 'ASC')->orderBy('order', 'ASC')->get();
        }
        foreach ($arrayCharacters as $c) {

            $usageCount = Value::where('character_id', '=', $c->id)
                ->where('header_id', '>=', 2)
                ->where('value', '<>', '')
                ->count();
            $c->usage_count = $usageCount;
        }
        return $arrayCharacters;
    }

    public function getDefaultCharacters() {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $standardCharacters = StandardCharacter::all();

        foreach ($standardCharacters as $eachCharacter) {
            $tempArray = Character::where('username', '=', $eachCharacter->username)
                ->where('name', '=', $eachCharacter->name)
                ->get();
            $tempCount = 0;
            foreach ($tempArray as $tempCharacter) {
                $tempCount += $tempCharacter->usage_count;
            }
            $eachCharacter->usage_count = $tempCount;
        }
        $standardCharacters = $standardCharacters->toArray();

        $userCharacters = Character::where('standard', '=', 0)
            ->whereRaw('username LIKE CONCAT("%", owner_name)')
            ->get();
        foreach ($userCharacters as $eachCharacter) {
            $tempArray = Character::where('username', '=', $eachCharacter->username)
                ->where('name', '=', $eachCharacter->name)
                ->get();
            $tempCount = 0;
            foreach ($tempArray as $tempCharacter) {
                $tempCount += $tempCharacter->usage_count;
            }
            $eachCharacter->usage_count = $tempCount;
        }
        $userCharacters = $userCharacters->toArray();

        $defaultCharacters = array_merge($standardCharacters, $userCharacters);

        return $defaultCharacters;
    }

    public function removeString($string, $compareStr) {
        $string = str_replace($compareStr, '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9, \-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }


    public function getStandardCharacters() {
        $result = $this->getDefaultCharacters();

        return $result;
    }

    public function getUserTags(Request $request, $userId) {
        $userTags = UserTag::where('user_id', '=', $userId)->get();

        return $userTags;
    }

    public function createUserTag(Request $request) {
        $userTag = new UserTag([
            'tag_name' => $request->input('user_tag'),
            'user_id' => $request->input('user_id')
        ]);
        $userTag->save();

        return $userTag;
    }

    public function removeUserTag(Request $request) {
        $userTag = UserTag::where([
            ['tag_name', '=', $request->input('user_tag')],
            ['user_id', '=', $request->input('user_id')],
        ])->delete();

        $userTags = UserTag::where('user_id', '=', $request->input('user_id'))->get();
        return $userTags;
    }

    public function deleteHeader(Request $request, $headerId) {
        $headers = Header::where('id', '=', $headerId)->delete();
        $values = Value::where('header_id', '=', $headerId)->delete();

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }
    public function changeTaxon(Request $request, $taxon) {
        $user = User::where('id', '=', Auth::id())->first();
        $user->taxon = $taxon;
        $user->save();

        $data = [
            'taxon' => $user->taxon
        ];

        return $data;
    }

    public function storeCharacter(Request $request) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $character = new Character([
            'name' => $request->input('name'),
            'method_from' => $request->input('method_from'),
            'method_to' => $request->input('method_to'),
            'method_include' => $request->input('method_include'),
            'method_exclude' => $request->input('method_exclude'),
            'method_where' => $request->input('method_where'),
            'unit' => $request->input('unit'),
            'standard' => $request->input('standard'),
            'creator' => $request->input('creator'),
            'username' => $request->input('username'),
            'owner_name' => $username,
            'usage_count' => 0,
            'show_flag' => $request->input('show_flag'),
            'standard_tag' => $request->input('standard_tag'),
            'summary' => $request->input('summary'),
        ]);

        $character->save();
        $character->order = $character->id;
        $character->save();

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'defaultCharacters' => $returnDefaultCharacters
        ];

        return $data;

    }
    public function getCharacter(Request $request, $userId) {
        $user = User::where('id', '=', $userId)->first();
        $username = explode('@', $user['email'])[0];

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnTaxon = $user->taxon;
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'taxon' => $returnTaxon
        ];

        return $data;
    }

    public function deleteCharacter(Request $request, $userId, $characterId) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        if (Character::where('standard_tag', '=', Character::where('id', '=', $characterId)->first()->standard_tag)
                ->where('owner_name', '=', $username)
                ->count() < 2) {
            UserTag::where('tag_name', '=', Character::where('id', '=', $characterId)->first()->standard_tag)
                ->where('user_id', '=', Auth::id())
                ->delete();
        }


        $character = Character::where('id', '=', $characterId)->delete();
        if (Value::where('character_id', '=', $characterId)->first()) {
            Value::where('character_id', '=', $characterId)->delete();
        }


        $characters = Character::where('owner_name', '=', $username)->orderBy('standard_tag', 'ASC')->get();

        $usedCharacters = Character::where('owner_name', '=', $username)->get();
        foreach ($usedCharacters as $usedCharacter) {
            $usedCharacter->username = str_replace($username . ', ', '', $usedCharacter->username);
            $usedCharacter->save();
        }

        if (!Character::where('owner_name', '=', $username)->first()) {
            Header::where('user_id', '=', Auth::id())->delete();
        }

        $defaultCharacters = $this->getDefaultCharacters();



        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnUserTags = UserTag::where('user_id', '=', Auth::id())->get();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'userTags' => $returnUserTags,
            'defaultCharacters' => $defaultCharacters
        ];

        return $data;
    }

    public function storeMatrix(Request $request) {
        $user = User::where('id', '=', $request->input('user_id'))->first();
        $username = explode('@', $user['email'])[0];
        $characters = Character::where('owner_name', '=', $username)->get();
        $columnCount = (int)$request->input('column_count');
        $user->taxon = $request->input('taxon');
        $user->save();
        $previousHeaderCount = Header::where('user_id', '=', $request->input('user_id'))->count();
        if ($previousHeaderCount < $columnCount) {
            for ($i = 0; $i < ($columnCount - $previousHeaderCount); $i++) {
                $header = Header::create([
                    'user_id' => $request->input('user_id'),
                    'header' => 'Specimen' . ($previousHeaderCount + $i + 1)
                ]);
            }
        }

        $headers = Header::where('user_id', '=', $request->input('user_id'))->get();

        foreach ($characters as $eachCharacter) {
            $value = Value::where('character_id', '=', $eachCharacter->id)
                ->where('header_id', '=', 1)
                ->first();
            if ($value == null) {
                Value::create([
                    'character_id' => $eachCharacter->id,
                    'header_id' => 1,
                    'value' => $eachCharacter->name,
                ]);

            }
            foreach($headers as $header) {
                $value = Value::where('character_id', '=', $eachCharacter->id)
                    ->where('header_id', '=', $header->id)
                    ->first();
                if ($value == null) {
                    Value::create([
                        'character_id' => $eachCharacter->id,
                        'header_id' => $header->id,
                        'value' => '',
                    ]);
                }
            }
        }

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnTaxon = $user->taxon;
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'taxon' => $returnTaxon,
        ];

        return $data;
    }

    public function addMoreColumn(Request $request, $columnCount) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        $oldHeaderCount = Header::where('user_id', '=', Auth::id())->count();
        $characters = Character::where('owner_name', '=', $username)->get();
        for ($i = 0; $i < ($columnCount - $oldHeaderCount); $i++) {
            for ($j = 0; $j < $columnCount; $j++) {
                if (!Header::where('header', '=', ('Specimen' . ($j + 1)))->where('user_id', '=', Auth::id())->first()) {
                    $header = Header::create([
                        'user_id' => Auth::id(),
                        'header' => 'Specimen' . ($j + 1)
                    ]);
                    foreach ($characters as $eachCharacter) {
                        Value::create([
                            'character_id' => $eachCharacter->id,
                            'header_id' => $header->id,
                            'value' => '',
                        ]);
                    }
                    break;
                }
            }
        }

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnTaxon = $user->taxon;

        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'taxon' => $returnTaxon,
        ];

        return $data;
    }

    public function addCharacter(Request $request) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $character = new Character([
            'name' => $request->input('name'),
            'method_from' => $request->input('method_from'),
            'method_to' => $request->input('method_to'),
            'method_include' => $request->input('method_include'),
            'method_exclude' => $request->input('method_exclude'),
            'method_where' => $request->input('method_where'),
            'unit' => $request->input('unit'),
            'standard' => $request->input('standard'),
            'creator' => $request->input('creator'),
            'username' => $request->input('username'),
            'owner_name' => $username,
            'usage_count' => 0,
            'show_flag' => $request->input('show_flag'),
            'standard_tag' => $request->input('standard_tag'),
            'summary' => $request->input('summary'),
        ]);

        $character->save();

        $character->order = $character->id;
        $character->save();

        $headers = Header::where('user_id', '=', Auth::id())->get();

        $value = Value::create([
            'header_id' => 1,
            'character_id' => $character->id,
            'value' => $character->name,
        ]);
        foreach ($headers as $eachHeader) {
            $value = Value::create([
                'header_id' => $eachHeader->id,
                'character_id' => $character->id,
                'value' => '',
            ]);
        }

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnTaxon = $user->taxon;

        $returnTaxon = $user->taxon;

        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'taxon' => $returnTaxon,
            'defaultCharacters' => $returnDefaultCharacters,
        ];

        return $data;
    }

    public function updateValue(Request $request) {
        $value = Value::where('id', '=', $request->input('id'))->first();

        $v = $request->input('value');
        $value->value = $v;
//        if (is_numeric($v)) {
//            $value->value = $v;
//        } else {
//            $varr = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$v);
//            if (count($varr)==2 && is_numeric($varr[0])) {
//                $c = Character::find($value->character_id);
//                if ($c->unit == $varr[1]) {
//                    $value->value = $varr[0];
//                } else {
//                    return ['error_input' => 1];
//                }
//            }
//        }

        $value->save();

        $character = Character::where('id', '=', $value->character_id)->first();

        $character->usage_count = Value::where('character_id', '=', $character->id)
            ->where('value', '<>', '')
            ->where('value', '<>', null)
            ->where('value', '<>', $character->name)
            ->count();
        $character->save();

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'error_input' => 0,
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'defaultCharacters' => $returnDefaultCharacters,
        ];

        return $data;
    }

    public function addStandardCharacter(Request $request) {
        $standardCharacters = $request->all();
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        foreach ($standardCharacters as $eachCharacter) {
            $character = new Character([
                'name' => $eachCharacter['name'],
                'method_from' => $eachCharacter['method_from'],
                'method_to' => $eachCharacter['method_to'],
                'method_include' => $eachCharacter['method_include'],
                'method_exclude' => $eachCharacter['method_exclude'],
                'method_where' => $eachCharacter['method_where'],
                'unit' => $eachCharacter['unit'],
                'standard' => $eachCharacter['standard'],
                'creator' => $eachCharacter['creator'],
                'username' => $eachCharacter['username'],
                'owner_name' => $username,
                'usage_count' => 0,
                'show_flag' => $eachCharacter['show_flag'],
                'standard_tag' => $eachCharacter['standard_tag'],
                'summary' => $eachCharacter['summary'],
            ]);

            $character->save();

            $character->order = $character->id;
            $character->save();

            if (!UserTag::where('user_id', '=', Auth::id())->where('tag_name', '=', $eachCharacter['standard_tag'])->first()) {
                UserTag::create([
                    'user_id' => Auth::id(),
                    'tag_name' => $eachCharacter['standard_tag']
                ]);
            }
        }

        $returnCharacters = $this->getArrayCharacters();

        return $returnCharacters;
    }

    public function removeAllStandard(Request $request) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $characters = Character::where('owner_name', '=', $username)->where('standard', '=', 1)->get();
        foreach($characters as $eachCharacter) {
            if (Value::where('character_id', '=', $eachCharacter->id)->first()) {
                Value::where('character_id', '=', $eachCharacter->id)->delete();
            }
            if (Character::where('standard_tag', '=', $eachCharacter->standard_tag)->where('owner_name', '=', $username)->count() < 2) {
                UserTag::where('user_id', '=', Auth::id())->where('tag_name', '=', $eachCharacter->standard_tag)->delete();
            }
            $eachCharacter->delete();
        }
        if (!Character::where('username', '=', $username)->first()) {
            if (Header::where('user_id', '=', Auth::id())->first()) {
                Header::where('user_id', '=', Auth::id())->delete();
            }
        }

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnUserTags = UserTag::where('user_id', '=', Auth::id())->get();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'tags' => $returnUserTags,
        ];

        return $data;

    }

    public function showTabCharacter(Request $request, $tabName) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $allCharacters = Character::where('owner_name', '=', $username)->get();
        $characters = Character::where('standard_tag', '=', $tabName)->where('owner_name', '=', $username)->get();

        foreach ($allCharacters as $eachCharacter) {
            $eachCharacter->show_flag = false;
            $eachCharacter->save();
        }
        foreach ($characters as $character) {
            $character->show_flag = true;
            $character->save();
        }

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();

        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function removeAll(Request $request) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $characters = Character::where('owner_name', '=', $username)->where('standard', '=', 0)->get();
        foreach($characters as $eachCharacter) {
            if (Value::where('character_id', '=', $eachCharacter->id)->first()) {
                Value::where('character_id', '=', $eachCharacter->id)->delete();
            }
            if (Character::where('standard_tag', '=', $eachCharacter->standard_tag)->where('owner_name', '=', $username)->count() < 2) {
                UserTag::where('user_id', '=', Auth::id())->where('tag_name', '=', $eachCharacter->standard_tag)->delete();
            }
            $eachCharacter->delete();
        }

        if (!Character::where('owner_name', '=', $username)->first()) {
            Header::where('user_id', '=', Auth::id())->delete();
        }

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function updateUnit(Request $request) {
        $character = Character::where('id', '=', $request->input('character_id'))->first();
        $oldUnit = $character->unit;
        if ($character->unit != $request->input('unit')) {
            $character->unit = $request->input('unit');
            $character->save();
            $values = Value::where('character_id', '=', $character->id)->where('header_id', '<>', 1)->get();
            foreach ($values as $value) {
                if ($value->value) {
                    switch ($oldUnit) {
                        case "mm":
                            switch ($request->input('unit')) {
                                case "cm":
                                    $value->value = round((float)$value->value / 10, 2);
                                    $value->save();
                                    break;
                                case "dm":
                                    $value->value = round((float)$value->value / 100, 2);
                                    $value->save();
                                    break;
                                case "m":
                                    $value->value = round((float)$value->value / 1000, 2);
                                    $value->save();
                                    break;
                                default:
                                    break;
                            }
                            break;
                        case "cm":
                            switch ($request->input('unit')) {
                                case "mm":
                                    $value->value = round((float)$value->value * 10, 2);
                                    $value->save();
                                    break;
                                case "dm":
                                    $value->value = round((float)$value->value / 10, 2);
                                    $value->save();
                                    break;
                                case "m":
                                    $value->value = round((float)$value->value / 100, 2);
                                    $value->save();
                                    break;
                                default:
                                    break;
                            }
                            break;
                        case "dm":
                            switch ($request->input('unit')) {
                                case "mm":
                                    $value->value = round((float)$value->value * 100, 2);
                                    $value->save();
                                    break;
                                case "cm":
                                    $value->value = round((float)$value->value * 10, 2);
                                    $value->save();
                                    break;
                                case "m":
                                    $value->value = round((float)$value->value / 10, 2);
                                    $value->save();
                                    break;
                                default:
                                    break;
                            }
                            break;
                        case "m":
                            switch ($request->input('unit')) {
                                case "mm":
                                    $value->value = round((float)$value->value * 1000, 2);
                                    $value->save();
                                    break;
                                case "cm":
                                    $value->value = round((float)$value->value * 100, 2);
                                    $value->save();
                                    break;
                                case "dm":
                                    $value->value = round((float)$value->value * 10, 2);
                                    $value->save();
                                    break;
                                default:
                                    break;
                            }
                            break;
                        default:
                            break;
                    }
                }

            }

        }


        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function exportDescription(Request $request) {
        $fileName = $request->input('taxon');
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        $textLines = explode('<br/>', $request->input('template'));
        $textrun = $section->addTextRun();
        foreach ($textLines as $eachText) {
            $eachText = str_replace('<b>', '', $eachText);
            $eachText = str_replace('</b>', '', $eachText);
            $separatedTexts = explode(':', $eachText);
            if (count($separatedTexts) > 1) {
                if ($separatedTexts[1] != ' . ') {
                    $textrun->addText($separatedTexts[0] . ': ', ['bold' => true]);
                    $textrun->addText($separatedTexts[1]);
                    $textrun->addTextBreak();
                }

            }
        }
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($fileName . '.docx');

        return array(
            'is_scucess'    =>  1,
            'doc_url'       =>  '/chrecorder/public/' . $fileName . '.docx',
        );
    }

    public function updateSummary(Request $request) {
        $character = Character::where('id', '=', $request->input('character_id'))->first();
        $character->summary = $request->input('summary');
        $character->save();

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function updateCharacter(Request $request) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $character = Character::where('id', '=', $request->input('id'))->first();

        $oldTag = $character->standard_tag;

        $character->name = $request->input('name');
        $character->method_from = $request->input('method_from');
        $character->method_to = $request->input('method_to');
        $character->method_include = $request->input('method_include');
        $character->method_exclude = $request->input('method_exclude');
        $character->method_where = $request->input('method_where');
        $character->unit = $request->input('unit');
        $character->standard = $request->input('standard');
        $character->creator = $request->input('creator');
        $character->username = $request->input('username');
        $character->usage_count = $request->input('usage_count');
        $character->show_flag = $request->input('show_flag');
        $character->standard_tag = $request->input('standard_tag');
        $character->summary = $request->input('summary');

        $character->save();

        if ($oldTag != $character->standard_tag) {
            if (Character::where('owner_name', '=', $username)->where('standard_tag', '=', $oldTag)->first() == null) {
                UserTag::where('tag_name', '=', $oldTag)->where('user_id', '=', Auth::id())->delete();
            }

            if (UserTag::where('tag_name', '=', $character->standard_tag)->where('user_id', '=', Auth::id())->first() == null) {
                $userTag = new UserTag([
                    'tag_name'  =>  $character->standard_tag,
                    'user_id'   =>  Auth::id()
                ]);

                $userTag->save();
            }
        }

        $returnUserTags = UserTag::where('user_id', '=', Auth::id())->get();
        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'defaultCharacters' => $returnDefaultCharacters,
            'userTags'  => $returnUserTags
        ];

        return $data;
    }

    public function changeOrder(Request $request) {
        $characterId = $request->input('characterId');
        $order = $request->input('order');
        if ($order == 'up') {
            $character = Character::where('id', '=', $characterId)->first();
            $beforeCharacter = Character::where('order', '=', ($character->order - 1))->first();
            $character->order = $character->order - 1;
            $character->save();
            $beforeCharacter->order = $beforeCharacter->order + 1;
            $beforeCharacter->save();
        } else if ($order == 'down') {
            $character = Character::where('id', '=', $characterId)->first();
            $afterCharacter = Character::where('order', '=', ($character->order + 1))->first();
            $character->order = $character->order + 1;
            $character->save();
            $afterCharacter->order = $afterCharacter->order - 1;
            $afterCharacter->save();
        }

        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function updateHeader(Request $request) {
        $header = Header::where('id', '=', $request->input('id'))->first();
        $header->header = $request->input('header');
        $header->save();
        $returnHeaders = $this->getHeaders();

        $data = [
            'headers' => $returnHeaders,
        ];

        return $data;
    }

    public function getUsage(Request $request, $characterId) {
        $character = Character::where('id', '=', $characterId)->first();
        $usage = 0;
        if ($character) {
            $usage = $character->usage_count;
        }

        $data = [
            'usage_count' => $usage
        ];

        return $data;
    }
    public function getColorDetails(Request $request, $valueId) {
        $colorDetails = ColorDetails::where('value_id', '=', $valueId)->get();

        $data = [
            'colorDetails' => $colorDetails
        ];

        return $data;
    }

    public function saveColorValue(Request $request) {
        $colorValues = $request->all();

        $characterName = Character::where('id', '=', Value::where('id', '=', $colorValues[0]['value_id'])->first()->character_id)->first()->name;
        foreach ($colorValues as $eachColor) {
            if (array_key_exists('id', $eachColor)) {
                $color = ColorDetails::where('id', '=', $eachColor['id'])->first();
                $color->value_id = $eachColor['value_id'];
                $color->negation = $eachColor['negation'];
                $color->pre_constraint = $eachColor['pre_constraint'];
                $color->brightness = $eachColor['brightness'];
                $color->reflectance = $eachColor['reflectance'];
                $color->saturation = $eachColor['saturation'];
                $color->colored = $eachColor['colored'];
                $color->multi_colored = $eachColor['multi_colored'];
                $color->post_constraint = $eachColor['post_constraint'];

                $color->save();
            } else {
                $color = new ColorDetails([
                    'value_id' => $eachColor['value_id'],
                    'negation' => isset($eachColor['negation']) ? $eachColor['negation'] : null,
                    'pre_constraint' => isset($eachColor['pre_constraint']) ? $eachColor['pre_constraint'] : null,
                    'brightness' => isset($eachColor['brightness']) ? $eachColor['brightness'] : null,
                    'reflectance' => isset($eachColor['reflectance']) ? $eachColor['reflectance'] : null,
                    'saturation' => isset($eachColor['saturation']) ? $eachColor['saturation'] : null,
                    'colored' => isset($eachColor['colored']) ? $eachColor['colored'] : null,
                    'multi_colored' => isset($eachColor['multi_colored']) ? $eachColor['multi_colored'] : null,
                    'post_constraint' => isset($eachColor['post_constraint']) ? $eachColor['post_constraint'] : null,
                ]);

                $color->save();
            }
        }


        $characters = Character::where('name', '=', $characterName)->get();

        $constraints = $this->getDefaultConstraint($characterName);

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];

        return $data;
    }

    public function getConstraint(Request $request, $characterName) {
        $characters = Character::where('name', '=', $characterName)->get();

        $preList = ['longitudinally'];
        $postList = ['when young'];
        foreach ($characters as $eachCharacter) {
            $values = Value::where('character_id', '=', $eachCharacter->id)->where('header_id', '<>', 1)->get();
            foreach($values as $eachValue) {
                $details = ColorDetails::where('value_id', '=', $eachValue->id)->get();
                foreach ($details as $each) {
                    if ($each->pre_constraint != null && $each->pre_constraint != '' && $each->pre_constraint != 'undefined' && $each->pre_constraint != 'null') {
                        if (!in_array($each->pre_constraint, $preList)) {
                            array_push($preList, $each->pre_constraint);
                        }
                    }
                    if ($each->post_constraint != null && $each->post_constraint != '' && $each->post_constraint != 'undefined' && $each->post_constraint != 'null') {
                        if (!in_array($each->post_constraint, $postList)) {
                            array_push($postList, $each->post_constraint);
                        }
                    }
                }
                $details = NonColorDetails::where('value_id', '=', $eachValue->id)->get();
                foreach ($details as $each) {
                    if ($each->pre_constraint != null && $each->pre_constraint != '' && $each->pre_constraint != 'undefined' && $each->pre_constraint != 'null') {
                        if (!in_array($each->pre_constraint, $preList)) {
                            array_push($preList, $each->pre_constraint);
                        }
                    }
                    if ($each->post_constraint != null && $each->post_constraint != '' && $each->post_constraint != 'undefined' && $each->post_constraint != 'null') {
                        if (!in_array($each->post_constraint, $postList)) {
                            array_push($postList, $each->post_constraint);
                        }
                    }
                }
            }
        }

        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $preList,
            'postList' => $postList,
        ];

        return $data;
    }

    public function getNonColorDetails(Request $request, $valueId) {
        $nonColorDetails = NonColorDetails::where('value_id', '=', $valueId)->get();

        $data = [
            'nonColorDetails' => $nonColorDetails
        ];

        return $data;
    }

    public function saveNonColorValue(Request $request) {
        $nonColorValues = $request->all();

        $characterName = Character::where('id', '=', Value::where('id', '=', $nonColorValues[0]['value_id'])->first()->character_id)->first()->name;

        foreach ($nonColorValues as $eachValue) {
            if (array_key_exists('id', $eachValue)) {
                $value = NonColorDetails::where('id', '=', $eachValue['id'])->first();
                $value->value_id = $eachValue['value_id'];
                $value->negation = $eachValue['negation'];
                $value->pre_constraint = $eachValue['pre_constraint'];
                $value->main_value = $eachValue['main_value'];
                $value->post_constraint = $eachValue['post_constraint'];

                $value->save();
            } else {
                $value = new NonColorDetails([
                    'value_id' => $eachValue['value_id'],
                    'negation' => isset($eachValue['negation']) ? $eachValue['negation'] : null,
                    'pre_constraint' => isset($eachValue['pre_constraint']) ? $eachValue['pre_constraint'] : null,
                    'main_value' => isset($eachValue['main_value']) ? $eachValue['main_value'] : null,
                    'post_constraint' => isset($eachValue['post_constraint']) ? $eachValue['post_constraint'] : null,
                ]);

                $value->save();
            }
        }

        $characters = Character::where('name', '=', $characterName)->get();

        $constraints = $this->getDefaultConstraint($characterName);

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];

        return $data;
    }

    public function getDefaultConstraint($characterName) {
        $characters = Character::where('name', '=', $characterName)->get();

        $preList = ['longitudinally'];
        $postList = ['when young'];
        foreach ($characters as $eachCharacter) {
            $values = Value::where('character_id', '=', $eachCharacter->id)->where('header_id', '<>', 1)->get();
            foreach($values as $eachValue) {
                $details = ColorDetails::where('value_id', '=', $eachValue->id)->get();
                foreach ($details as $each) {
                    if ($each->pre_constraint != null && $each->pre_constraint != '' && $each->pre_constraint != 'undefined' && $each->pre_constraint != 'null') {
                        if (!in_array($each->pre_constraint, $preList)) {
                            array_push($preList, $each->pre_constraint);
                        }
                    }
                    if ($each->post_constraint != null && $each->post_constraint != '' && $each->post_constraint != 'undefined' && $each->post_constraint != 'null') {
                        if (!in_array($each->post_constraint, $postList)) {
                            array_push($postList, $each->post_constraint);
                        }
                    }
                }
                $details = NonColorDetails::where('value_id', '=', $eachValue->id)->get();
                foreach ($details as $each) {
                    if ($each->pre_constraint != null && $each->pre_constraint != '' && $each->pre_constraint != 'undefined' && $each->pre_constraint != 'null') {
                        if (!in_array($each->pre_constraint, $preList)) {
                            array_push($preList, $each->pre_constraint);
                        }
                    }
                    if ($each->post_constraint != null && $each->post_constraint != '' && $each->post_constraint != 'undefined' && $each->post_constraint != 'null') {
                        if (!in_array($each->post_constraint, $postList)) {
                            array_push($postList, $each->post_constraint);
                        }
                    }
                }
            }
        }

        $data = [
            'preList' => $preList,
            'postList' => $postList,
        ];

        return $data;
    }

    public function removeColorValue(Request $request) {
        $valueId = $request->input('value_id');

        ColorDetails::where('value_id', '=', $valueId)->delete();

        $returnValues = $this->getValuesByCharacter();

        $characterName = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first()->name;

        $constraints = $this->getDefaultConstraint($characterName);

        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];
        return $data;
    }

    public function removeNonColorValue(Request $request) {
        $valueId = $request->input('value_id');

        NonColorDetails::where('value_id', '=', $valueId)->delete();

        $returnValues = $this->getValuesByCharacter();

        $characterName = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first()->name;

        $constraints = $this->getDefaultConstraint($characterName);
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];

        return $data;
    }

    public function getColorValues(Request $request) {
        $valueIds = $request->input('value_id');

        $colorValues = ColorDetails::whereIn('value_id', $valueIds)->get();

        $data = [
            'colorValues' => $colorValues,
        ];

        return $data;
    }
}
