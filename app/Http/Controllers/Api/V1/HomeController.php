<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\MyEvent;
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
use DB;

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
        $values = Value::join('characters','characters.id','=','values.character_id')->where('characters.owner_name','=',$username)->select('values.id as id','values.character_id as character_id', 'values.header_id as header_id','values.value', 'characters.unit as unit', 'characters.summary as summary')->get();
        $colorDetails = ColorDetails::join('values','values.id','=','color_details.value_id')->join('characters','characters.id','=','values.character_id')->where('characters.owner_name','=',$username)->get();
        $nonColorDetails = NonColorDetails::join('values','values.id','=','non_color_details.value_id')->join('characters','characters.id','=','values.character_id')->where('characters.owner_name','=',$username)->get();

        $characters = [];
        $valueFlag = [];
        $character_id = [];
        $header_id = [];
        $value_id = [];
        $char_count = 0;
        $i = 0;
        foreach ($headers as $header){
            $header_id[$header->id] = $i;
            $i = $i + 1;
        }
        foreach ($allCharacters as $ac){
            $character_id[$ac->id] = $char_count;
            $characters[$char_count] = [];
            for ($j = 0; $j < $i ; $j ++){
                array_push($characters[$char_count],[]);
            }
            $char_count = $char_count + 1;
        }

        foreach ($values as $val){
            //$val->value = substr($val->value, 0, -1);
            if (!$val->value){
                $val->value = '';
            }
            $characters[$character_id[$val->character_id]][$header_id[$val->header_id]] = $val;
            $value_id[$val->id] = 1;
        }

        foreach ($colorDetails as $col){
            if ($value_id[$col->value_id]){
                if ($value_id[$col->value_id] == 1){
                    $value_id[$col->value_id] = 2;
                    $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value = '';
                }
                $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value = $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value . ($col->negation ? ($col->negation . ' ') : '') .
                    ($col->pre_constraint ? ($col->pre_constraint . ' ') : '') .
                    ($col->certainty_constraint ? ($col->certainty_constraint . ' ') : '') .
                    ($col->degree_constraint ? ($col->degree_constraint . ' ') : '') .
                    ($col->brightness ? ($col->brightness . ' ') : '') .
                    ($col->reflectance ? ($col->reflectance . ' ') : '') .
                    ($col->saturation ? ($col->saturation . ' ') : '') .
                    ($col->colored ? ($col->colored . ' ') : '') .
                    ($col->multi_colored ? ($col->multi_colored . ' ') : '') .
                    ($col->post_constraint ? ($col->post_constraint . ' ') : '') ;
                if ($characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value != '') {
                    $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value = substr($characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value, 0, -1);
                    $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value = $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value . '; ';
                }
            }
        }

        foreach ($nonColorDetails as $nonCol){
            if ($value_id[$nonCol->value_id]){
                if ($value_id[$nonCol->value_id] == 1){
                    $value_id[$nonCol->value_id] = 2;
                    $characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value = '';
                }
                $characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value = $characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value . ($nonCol->negation ? ($nonCol->negation . ' ') : '') .
                    ($nonCol->pre_constraint ? ($nonCol->pre_constraint . ' ') : '') .
                    ($nonCol->certainty_constraint ? ($nonCol->certainty_constraint . ' ') : '') .
                    ($nonCol->degree_constraint ? ($nonCol->degree_constraint . ' ') : '') .
                    ($nonCol->main_value ? ($nonCol->main_value . ' ') : '') .
                    ($nonCol->post_constraint ? ($nonCol->post_constraint . ' ') : '');
                if ($characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value != '') {
                    $characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value = substr($characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value, 0, -1);
                    $characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value = $characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value . '; ';
                }
            }
        }

        // foreach ($allCharacters as $eachCharacter) {
        //     $value_array = [];
        //     foreach ($headers as $header) {
        //         $flag = false;
        //         foreach ($values as $val){
        //             if ($val->character_id == $eachCharacter->id && $val->header_id == $header->id){
        //                 $value = $val;
        //                 $flag = true;
        //                 break;
        //             }
        //         }
        //         if ($flag) {
        //             $currentCharacterName = $eachCharacter->name;
        //             if (substr($currentCharacterName, 0, 5) == 'Color' && $value->header_id != 1) {
        //                 foreach($colorDetails as $col){
        //                     if ($col->value_id == $value->id){
        //                         $value->value = $value->value . ($eachColor->negation ? ($eachColor->negation . ' ') : '') .
        //                             ($eachColor->pre_constraint ? ($eachColor->pre_constraint . ' ') : '') .
        //                             ($eachColor->certainty_constraint ? ($eachColor->certainty_constraint . ' ') : '') .
        //                             ($eachColor->degree_constraint ? ($eachColor->degree_constraint . ' ') : '') .
        //                             ($eachColor->brightness ? ($eachColor->brightness . ' ') : '') .
        //                             ($eachColor->reflectance ? ($eachColor->reflectance . ' ') : '') .
        //                             ($eachColor->saturation ? ($eachColor->saturation . ' ') : '') .
        //                             ($eachColor->colored ? ($eachColor->colored . ' ') : '') .
        //                             ($eachColor->multi_colored ? ($eachColor->multi_colored . ' ') : '') .
        //                             ($eachColor->post_constraint ? ($eachColor->post_constraint . ' ') : '') ;
        //                         if ($value->value != '') {
        //                             $value->value = substr($value->value, 0, -1);
        //                             $value->value = $value->value . '; ';
        //                         }
        //                     }
        //                 }
        //                 if ($value->value != '') {
        //                     $value->value = substr($value->value, 0, -1);
        //                 }

        //             } else if (!$this->checkNumericalCharacter($currentCharacterName) && $value->header_id != 1) {
        //                 foreach($nonColorDetails as $nonCol){
        //                     if ($nonCol->value_id == $value->id){
        //                         $value->value = $value->value . ($eachValue->negation ? ($eachValue->negation . ' ') : '') .
        //                             ($eachValue->pre_constraint ? ($eachValue->pre_constraint . ' ') : '') .
        //                             ($eachValue->certainty_constraint ? ($eachValue->certainty_constraint . ' ') : '') .
        //                             ($eachValue->degree_constraint ? ($eachValue->degree_constraint . ' ') : '') .
        //                             ($eachValue->main_value ? ($eachValue->main_value . ' ') : '') .
        //                             ($eachValue->post_constraint ? ($eachValue->post_constraint . ' ') : '');
        //                         if ($value->value != '') {
        //                             $value->value = substr($value->value, 0, -1);
        //                             $value->value = $value->value . '; ';
        //                         }
        //                     }
        //                 }
                        
        //                 if ($value->value != '') {
        //                     $value->value = substr($value->value, 0, -1);
        //                 }
        //             }

        //             // $value->username = $eachCharacter->username;
        //             // $value->unit = $eachCharacter->unit;
        //             // $value->summary = $eachCharacter->summary;
        //             // $value->standard = $eachCharacter->standard;
        //             array_push($value_array, $value);
        //         }

        //     }
        //     array_push($characters, $value_array);
        // }

        // event(new MyEvent());

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

        $arrayCharacters = Character::where('owner_name', '=', $username)->orderBy('order', 'ASC')->get();
        $usageCounts = [];
        $usageCounts = Character::where('owner_name','=',$username)->join('values','values.character_id','=','characters.id')->where('header_id','>=',2)->where('value','<>','')->select('characters.id',DB::raw('count(values.id) as usageCount'))->groupBy('characters.id')->get();

        foreach($usageCounts as $uc){
            foreach($arrayCharacters as $ac){
                if ($ac->id == $uc->id){
                    $ac->usageCount = $uc->usageCount;
                    break;
                }
            }
        }
        return $arrayCharacters;
    }

    public function getDefaultCharacters() {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $standardCharacters = StandardCharacter::all();
        $standardUsages = Character::join('standard_characters','standard_characters.name','=','characters.name')->where('standard_characters.username','=','characters.username')->select('standard_characters.id as id', DB::raw('sum(characters.usage_count) as usage_count'))->groupBy('standard_characters.id')->get();

        foreach ($standardUsages as $su) {
            foreach($standardCharacters as $sc){
                if ($sc->id == $su->id){
                    $sc->usage_count = $su->usage_count;
                    break;
                }
            }
        }
        
        foreach($standardCharacters as $sc){
            if (!$sc->usage_count){
                $sc->usage_count = 0;
            }
        }

        $standardCharacters = $standardCharacters->toArray();

        $userCharacters = Character::where('standard', '=', 0)
            ->whereRaw('username LIKE CONCAT("%", owner_name)')
            ->get();
        $userUsages = DB::table('characters as A')->join('characters as B', 'A.name', '=', 'B.name')->where('A.standard','=',0)->whereRaw('A.username like concat("%", A.owner_name)')->where('A.username','=','B.username')->select('A.id as id',DB::raw('sum(B.usage_count) as usage_count'))->groupBy('A.id')->get();
        foreach ($userUsages as $uu) {
            foreach ($userCharacters as $uc){
                if ($uc->id == $uu->id){
                    $uc->usage_count = $uu->usage_count;
                    break;
                }
            }
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
            'method_as' => $request->input('method_as'),
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

        
        $returnHeaders = [];
        $returnValues = [];
        $returnCharacters = [];
        $returnTaxon = '';
        $returnAllDetailValues = ['colorValues' => [], 'nonColorValues' => []];

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


//        $characters = Character::where('owner_name', '=', $username)->orderBy('standard_tag', 'ASC')->get();
        $characters = Character::where('owner_name', '=', $username)->get();

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
        $headers = Header::where('user_id', '=', $request->input('user_id'))->get();
        $previousHeaderCount = $headers->count();
        $sampleNum = 1;

        if ($previousHeaderCount < $columnCount) {
            for ($i = 0; $i < ($columnCount - $previousHeaderCount); $i++) {
                $flag = true;
                while ($flag){
                    $flag = false;
                    foreach ($headers as $header){
                        if ($header->header == 'Sample'.$sampleNum){
                            $sampleNum = $sampleNum + 1;
                            $flag = true;
                        }
                    }
                }
                $header = Header::create([
                    'user_id' => $request->input('user_id'),
                    'header' => 'Sample' . ($sampleNum),
                ]);
            }
        }

        $headers = Header::where('user_id', '=', $request->input('user_id'))->get();

        $headerValues = Value::where('header_id', '=', 1)->get();
        $values = Value::join('headers', 'headers.id', '=', 'values.header_id')->where('headers.user_id', '=', $request->input('user_id'))->get();
        $temp = [];
        $valueFlag = [];
        foreach ($characters as $eachCharacter) {
            $valueFlag[$eachCharacter->id] = [];
            foreach ($headers as $header){
                $valueFlag[$eachCharacter->id][$header->id] = 1;
            }
        }
        foreach ($values as $val){
            $valueFlag[$val->character_id][$val->header_id] = 0;
        }
        foreach ($characters as $eachCharacter) {
            $flag = true;
            foreach ($headerValues as $hv){
                if ($hv['character_id'] == $eachCharacter->id){
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                array_push($temp,[
                    'character_id' => $eachCharacter->id,
                    'header_id' => 1,
                    'value' => $eachCharacter->name,
                ]);

            }
            foreach($headers as $header) {
                if ($valueFlag[$eachCharacter->id][$header->id]) {
                    array_push($temp,[
                        'character_id' => $eachCharacter->id,
                        'header_id' => $header->id,
                        'value' => '',
                    ]);
                }
            }
        }
        Value::insert($temp);

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
                if (!Header::where('header', '=', ('Sample' . ($j + 1)))->where('user_id', '=', Auth::id())->first()) {
                    $header = Header::create([
                        'user_id' => Auth::id(),
                        'header' => 'Sample' . ($j + 1)
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
            'method_as' => $request->input('method_as'),
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

        $order = Character::max('order') + 1;
        $characters = Character::where('owner_name', '=', $username)->select('name','method_from','method_to','method_include','method_exclude','method_where','owner_name')->get();
        $userTags = UserTag::where('user_id', '=', Auth::id())->get();
        $newCharacters = [];
        $newUserTags = [];
        foreach ($standardCharacters as $eachCharacter) {
            $flag = true;
            foreach ($characters as $ch){
                if ($eachCharacter['name'] == $ch['name']
                  && $eachCharacter['method_from'] == $ch['method_from']
                  && $eachCharacter['method_to'] == $ch['method_to']
                  && $eachCharacter['method_include'] == $ch['method_include']
                  && $eachCharacter['method_exclude'] == $ch['method_exclude']
                  && $eachCharacter['method_where'] == $ch['method_where']){
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                array_push($newCharacters,[
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
                    'show_flag' => $eachCharacter['show_flag']?1:0,
                    'standard_tag' => $eachCharacter['standard_tag'],
                    'summary' => $eachCharacter['summary'],
                    'order' => $order,
                ]);
                $order = $order + 1;
                
                $flag = true;
                foreach ($userTags as $tag){
                    if ($tag['tag_name'] == $eachCharacter['standard_tag']){
                        $flag = false;
                        break;
                    }
                }
                foreach ($newUserTags as $tag){
                    if ($tag['tag_name'] == $eachCharacter['standard_tag']){
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    array_push($newUserTags,[
                        'user_id' => Auth::id(),
                        'tag_name' => $eachCharacter['standard_tag']
                    ]);
                }
            }
        }
        Character::insert($newCharacters);
        UserTag::insert($newUserTags);

        $returnCharacters = $this->getArrayCharacters();

        return $returnCharacters;
    }

    public function removeAllStandard(Request $request) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $st_tags = Character::where('owner_name', '=', $username)->groupBy('standard_tag')->having(DB::raw('sum(standard)'),'>',0)->having(DB::raw('sum(standard)-count(*)'),'<',2)->select('standard_tag')->get();
        $tags = [];
        foreach($st_tags as $tag){
            array_push($tags,$tag->standard_tag);
        }
        Character::where('owner_name', '=', $username)->where('standard', '=', 1)->delete();
        UserTag::where('user_id','=',Auth::id())->whereIn('tag_name',$tags)->delete();

        // $characters = Character::where('owner_name', '=', $username)->where('standard', '=', 1)->get();
        // foreach($characters as $eachCharacter) {
        //     if (Value::where('character_id', '=', $eachCharacter->id)->first()) {
        //         Value::where('character_id', '=', $eachCharacter->id)->delete();
        //     }
        //     if (Character::where('standard_tag', '=', $eachCharacter->standard_tag)->where('owner_name', '=', $username)->count() < 2) {
        //         UserTag::where('user_id', '=', Auth::id())->where('tag_name', '=', $eachCharacter->standard_tag)->delete();
        //     }
        //     $eachCharacter->delete();
        // }
        // if (!Character::where('username', '=', $username)->first()) {
        //     if (Header::where('user_id', '=', Auth::id())->first()) {
        //         Header::where('user_id', '=', Auth::id())->delete();
        //     }
        // }

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
            'delTags' => $tags,
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
            'is_success'    =>  1,
            'doc_url'       =>  '/chrecorder/public/' . $fileName . '.docx',
        );
    }

    public function exportDescriptionCsv(Request $request) {
        $fileName = $request->input('taxon');
        $userCharacters = $request->input('userCharacters');
        $headers = $request->input('headers');
        $values = $request->input('values');
        $userTags = $request->input('userTags');


        $htmlString = '<table>';

        if (count($headers) > 0) {
            $htmlString .= '<tr><th>Character</th><th>Summary</th>';
            foreach ($headers as $eachHeader) {
                if ($eachHeader['header'] != 'Character') {
                    $htmlString .= '<th>' . $eachHeader['header'] . '</th>';
                }
            }
        }
        foreach ($userTags as $eachTag) {

            foreach ($userCharacters as $eachUserCharacter) {
                if ($eachUserCharacter['standard_tag'] == $eachTag['tag_name']) {
                    $htmlString .= '<tr>';
                    foreach ($values as $eachRow) {
                        if ($eachUserCharacter['id'] == $eachRow[0]['character_id']) {
                            foreach ($eachRow as $eachValue) {
                                if ($eachValue['header_id'] == 1) {
                                    $htmlString .= '<td>' . $eachValue['value'] . '</td>';
                                    if (substr($eachValue['value'], 0, 6) == 'Length') {
                                        $htmlString .= '<td>' . $this->calcSummary($eachRow) . '</td>';
                                    } else {
                                        $htmlString .= '<td></td>';
                                    }
                                }
                            }
                            foreach ($eachRow as $eachValue) {
                                if ($eachValue['header_id'] != 1) {
                                    $htmlString .= '<td>' . $eachValue['value'] . '</td>';
                                }
                            }
                        }
                    }
                    $htmlString .= '</tr>';
                }

            }
        }

        $htmlString = $htmlString . '</table>';

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
        $writer->save($fileName . '.csv');

        return array(
            'is_success'    =>  1,
            'doc_url'       =>  '/chrecorder/public/' . $fileName .'.csv',
        );
    }

    public function calcSummary($arrayRow) {
        $returnValue = '';
        $sum = 0;
        $arrayLength = 0;
        $tempRpArray = [];
        $mean = '';
        $range = '';
        foreach ($arrayRow as $eachValue) {
            if ($eachValue['header_id'] != 1) {
                $sum = $sum + (float)$eachValue['value'];
                if (number_format((float)$eachValue['value'], 2, '.', '') != 0.00) {
                    array_push($tempRpArray, number_format((float)$eachValue['value'], 2, '.', ''));
                    $arrayLength++;
                }
            }
        }
        if (count($tempRpArray) > 0) {
            $mean = number_format((float)($sum / $arrayLength), 2, '.', '');

            sort($tempRpArray);

            $minValue = number_format((float)$tempRpArray[0], 2, '.', '');
            $maxValue = number_format((float)$tempRpArray[count($tempRpArray) - 1], 2, '.', '');
            $minPercentileValue = 0;
            $maxPercentileValue = 0;

            if (count($tempRpArray) % 2 == 0) {
                $minPercentileValue = $tempRpArray[count($tempRpArray) / 2 - 1];
                $maxPercentileValue = $tempRpArray[count($tempRpArray) / 2];
            } else {
                if (count($tempRpArray) == 1) {
                    $minPercentileValue = $tempRpArray[0];
                    $maxPercentileValue = $tempRpArray[0];
                } else {
                    $minPercentileValue = $tempRpArray[count($tempRpArray) / 2 - 1.5];
                    $maxPercentileValue = $tempRpArray[count($tempRpArray) / 2 + 0.5];
                }
            }

            $range = '';
            if ($minValue == $maxValue) {
                $range = (string)$minValue;
            } else {
                $range = '(' . (string)$minValue . '-)' . (string)$minPercentileValue . '-' . (string)$maxPercentileValue . '(-' . (string)$maxValue . ')';
            }

            $returnValue = 'mean=' . (string)$mean . ', ' . 'range=' . $range;
        }


        return $returnValue;
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
        $character->method_as = $request->input('method_as');
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
        $returnValues = $this->getValuesByCharacter();
        $user = User::where('id', '=', Auth::id())->first();

        $selectedCharacter = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first();
        $users = User::where('taxon', '=', $user->taxon)->get();

        $characterList = Character::where('name', '=', $selectedCharacter->name)->get()->toArray();

        $existDetails = [];

        foreach ($characterList as $eachCharacter) {
            $tempFlag = false;
            foreach ($users as $eachUser) {
                if (substr( $eachUser->email, 0, strlen($eachCharacter['owner_name']) + 1 ) == $eachCharacter['owner_name'] . '@') {
                    $tempFlag = true;
                }
            }
            if ($tempFlag) {
                $values = Value::where('character_id', '=', $eachCharacter['id'])->get();
                foreach ($values as $eachValue) {
                    $existColorDetails = ColorDetails::where('value_id', '=', $eachValue->id)->get()->toArray();
                    foreach ($existColorDetails as $eachColorDetails) {
                        $eachColorDetails['username'] = $eachCharacter['owner_name'];
                        array_push($existDetails, $eachColorDetails);
                    }
                }
            }
        }

        $data = [
            'colorDetails' => $colorDetails,
            'values' => $returnValues,
            'existColorDetails' => $existDetails
        ];

        return $data;
    }

    public function saveColorValue(Request $request) {
        $colorValues = $request->all();

        $colorDetails = ColorDetails::where('id', '=', $request->input('id'))->first();
        if ($request->input('id') && $colorDetails) {
            $colorDetails->value_id = $request->input('value_id');
            $colorDetails->negation = $request->input('negation');
            $colorDetails->pre_constraint = $request->input('pre_constraint');
            $colorDetails->certainty_constraint = $request->input('certainty_constraint');
            $colorDetails->degree_constraint = $request->input('degree_constraint');
            $colorDetails->pre_constraint = $request->input('pre_constraint');
            $colorDetails->brightness = $request->input('brightness');
            $colorDetails->reflectance = $request->input('reflectance');
            $colorDetails->saturation = $request->input('saturation');
            $colorDetails->colored = $request->input('colored');
            $colorDetails->multi_colored = $request->input('multi_colored');
            $colorDetails->post_constraint = $request->input('post_constraint');

            $colorDetails->save();
        } else {
            $color = new ColorDetails([
                'value_id' => $request->input('value_id'),
                'negation' => $request->input('negation') ? $request->input('negation') : null,
                'pre_constraint' => $request->input('pre_constraint') ? $request->input('pre_constraint') : null,
                'certainty_constraint' => $request->input('certainty_constraint') ? $request->input('certainty_constraint') : null,
                'degree_constraint' => $request->input('degree_constraint') ? $request->input('degree_constraint') : null,
                'brightness' => $request->input('brightness') ? $request->input('brightness') : null,
                'reflectance' => $request->input('reflectance') ? $request->input('reflectance') : null,
                'saturation' => $request->input('saturation') ? $request->input('saturation') : null,
                'colored' => $request->input('colored') ? $request->input('colored') : null,
                'multi_colored' => $request->input('multi_colored') ? $request->input('multi_colored') : null,
                'post_constraint' => $request->input('post_constraint') ? $request->input('post_constraint') : null,
            ]);

            $color->save();
        }

        $characterName = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first()->name;

        $returnColorDetails = ColorDetails::where('value_id', '=', $request->input('value_id'))->get();
//        $characters = Character::where('name', '=', $characterName)->get();

        $constraints = $this->getDefaultConstraint($characterName);

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllColorValues();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
            'colorDetails' => $returnColorDetails,
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
        $returnValues = $this->getValuesByCharacter();

        $user = User::where('id', '=', Auth::id())->first();

        $selectedCharacter = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first();
        $users = User::where('taxon', '=', $user->taxon)->get();

        $characterList = Character::where('name', '=', $selectedCharacter->name)->get()->toArray();

        $existDetails = [];

        foreach ($characterList as $eachCharacter) {
            $tempFlag = false;
            foreach ($users as $eachUser) {
                if (substr( $eachUser->email, 0, strlen($eachCharacter['owner_name']) + 1 ) == $eachCharacter['owner_name'] . '@') {
                    $tempFlag = true;
                }
            }
            if ($tempFlag) {
                $values = Value::where('character_id', '=', $eachCharacter['id'])->get();
                foreach ($values as $eachValue) {
                    $existNonColorDetails = NonColorDetails::where('value_id', '=', $eachValue->id)->get()->toArray();
                    foreach ($existNonColorDetails as $eachNonColorDetails) {
                        $eachNonColorDetails['username'] = $eachCharacter['owner_name'];
                        array_push($existDetails, $eachNonColorDetails);
                    }
                }
            }
        }


        $data = [
            'nonColorDetails' => $nonColorDetails,
            'values' => $returnValues,
            'existNonColorDetails' => $existDetails
        ];

        return $data;
    }

    public function saveNonColorValue(Request $request) {
        $nonColorValues = $request->all();

        $nonColorDetails = NonColorDetails::where('id', '=', $request->input('id'))->first();
        if ($request->input('id') && $nonColorDetails) {
            $nonColorDetails->value_id = $request->input('value_id');
            $nonColorDetails->negation = $request->input('negation');
            $nonColorDetails->pre_constraint = $request->input('pre_constraint');
            $nonColorDetails->certainty_constraint = $request->input('certainty_constraint');
            $nonColorDetails->degree_constraint = $request->input('degree_constraint');
            $nonColorDetails->main_value = $request->input('main_value');
            $nonColorDetails->post_constraint = $request->input('post_constraint');

            $nonColorDetails->save();
        } else {
            $nonColorDetails = new NonColorDetails([
                'value_id' => $request->input('value_id'),
                'negation' =>  $request->input('negation') ?  $request->input('negation') : null,
                'pre_constraint' =>  $request->input('pre_constraint') ?  $request->input('pre_constraint') : null,
                'certainty_constraint' =>  $request->input('certainty_constraint') ?  $request->input('certainty_constraint') : null,
                'degree_constraint' =>  $request->input('degree_constraint') ?  $request->input('degree_constraint') : null,
                'main_value' =>  $request->input('main_value') ?  $request->input('main_value') : null,
                'post_constraint' =>  $request->input('post_constraint') ?  $request->input('post_constraint') : null,
            ]);

            $nonColorDetails->save();
        }

        $characterName = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first()->name;


//        $characters = Character::where('name', '=', $characterName)->get();

        $constraints = $this->getDefaultConstraint($characterName);

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllColorValues();
        $returnNonColorDetails = NonColorDetails::where('value_id', '=', $request->input('value_id'))->get();

        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
            'nonColorDetails' => $returnNonColorDetails,
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

    public function removeEachColorDetails(Request $request) {
        $eachColorDetails = ColorDetails::where('id', '=', $request->input('id'))->first();
        if ($eachColorDetails) {
            $eachColorDetails->delete();
        }

        $returnColorDetails = ColorDetails::where('value_id', '=', $request->input('value_id'))->get();
        $returnValues = $this->getValuesByCharacter();
        $characterName = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first()->name;

        $constraints = $this->getDefaultConstraint($characterName);

        $returnAllDetailValues = $this->getAllColorValues();

        $data = [
            'colorDetails' => $returnColorDetails,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];

        return $data;
    }

    public function removeEachNonColorDetails(Request $request) {
        $eachNonColorDetails = NonColorDetails::where('id', '=', $request->input('id'))->first();

        if ($eachNonColorDetails) {
            $eachNonColorDetails->delete();
        }
        $returnValues = $this->getValuesByCharacter();

        $returnNonColorDetails = NonColorDetails::where('value_id', '=', $request->input('value_id'))->get();
        $characterName = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first()->name;

        $constraints = $this->getDefaultConstraint($characterName);

        $returnAllDetailValues = $this->getAllColorValues();

        $data = [
            'nonColorDetails' => $returnNonColorDetails,
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


        $characterName = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first()->name;

        $returnValues = $this->getValuesByCharacter();

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

    public function overwriteValue(Request $request) {
        $value = $request->all();

        $selectedValue = Value::where('id', '=', $value['id'])->first();

        $characterName = Character::where('id', '=', $value['character_id'])->first()->name;
        $values = Value::where('character_id', '=', $value['character_id'])->get();

        if ($selectedValue->value != null) {

            foreach ($values as $eachValue) {
                if ($eachValue->header_id != 1) {
                    $eachValue->value = $value['value'];
                    $eachValue->save();
                }
            }
        } else if (substr($characterName, 0, 5) === "Color") {
            $colorDetails = ColorDetails::where('value_id', '=', $selectedValue->id)->get();
            if ($colorDetails) {
                foreach ($values as $eachValue) {
                    if ($eachValue->id != $value['id']) {
                        ColorDetails::where('value_id', '=', $eachValue->id)->delete();
                        foreach ($colorDetails as $eachColorDetails) {
                            $otherColorDetail = new ColorDetails([
                                'value_id' => $eachValue->id,
                                'negation' => $eachColorDetails->negation,
                                'pre_constraint' => $eachColorDetails->pre_constraint,
                                'brightness' => $eachColorDetails->brightness,
                                'reflectance' => $eachColorDetails->reflectance,
                                'saturation' => $eachColorDetails->saturation,
                                'colored' => $eachColorDetails->colored,
                                'multi_colored' => $eachColorDetails->multi_colored,
                                'post_constraint' => $eachColorDetails->post_constraint,
                            ]);

                            $otherColorDetail->save();
                        }
                    }
                }
            }
        } else {
            $nonColorDetails = NonColorDetails::where('value_id', '=', $selectedValue->id)->get();
            if ($nonColorDetails) {
                foreach ($values as $eachValue) {
                    if ($eachValue->id != $value['id']) {
                        NonColorDetails::where('value_id', '=', $eachValue->id)->delete();
                        foreach ($nonColorDetails as $eachNonColorDetails) {
                            $otherNonColorDetail = new NonColorDetails([
                                'value_id' => $eachValue->id,
                                'negation' => $eachNonColorDetails->negation,
                                'pre_constraint' => $eachNonColorDetails->pre_constraint,
                                'main_value' => $eachNonColorDetails->main_value,
                                'post_constraint' => $eachNonColorDetails->post_constraint,
                            ]);

                            $otherNonColorDetail->save();
                        }
                    }
                }
            }
        }


        $returnValues = $this->getValuesByCharacter();

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

    public function keepExistValue(Request $request) {
        $value = $request->all();

        $selectedValue = Value::where('id', '=', $value['id'])->first();

        $characterName = Character::where('id', '=', $value['character_id'])->first()->name;
        $values = Value::where('character_id', '=', $value['character_id'])->get();

        if ($selectedValue->value != null) {

            foreach ($values as $eachValue) {
                if ($eachValue->header_id != 1
                    && $eachValue->value == null) {
                    $eachValue->value = $value['value'];
                    $eachValue->save();
                }
            }
        } else if (substr($characterName, 0, 5) === "Color") {
            $colorDetails = ColorDetails::where('value_id', '=', $selectedValue->id)->get();
            if ($colorDetails) {
                foreach ($values as $eachValue) {
                    if ($eachValue->id != $value['id']) {
                        $existColorDetails = ColorDetails::where('value_id', '=', $eachValue->id)->first();
                        if (!$existColorDetails) {
                            foreach ($colorDetails as $eachColorDetails) {
                                $otherColorDetail = new ColorDetails([
                                    'value_id' => $eachValue->id,
                                    'negation' => $eachColorDetails->negation,
                                    'pre_constraint' => $eachColorDetails->pre_constraint,
                                    'brightness' => $eachColorDetails->brightness,
                                    'reflectance' => $eachColorDetails->reflectance,
                                    'saturation' => $eachColorDetails->saturation,
                                    'colored' => $eachColorDetails->colored,
                                    'multi_colored' => $eachColorDetails->multi_colored,
                                    'post_constraint' => $eachColorDetails->post_constraint,
                                ]);

                                $otherColorDetail->save();
                            }
                        }

                    }
                }
            }
        } else {
            $nonColorDetails = NonColorDetails::where('value_id', '=', $selectedValue->id)->get();
            if ($nonColorDetails) {
                foreach ($values as $eachValue) {
                    if ($eachValue->id != $value['id']) {
                        $existNonColorDetails = NonColorDetails::where('value_id', '=', $eachValue->id)->first();
                        if (!$existNonColorDetails) {
                            foreach ($nonColorDetails as $eachNonColorDetails) {
                                $otherNonColorDetail = new NonColorDetails([
                                    'value_id' => $eachValue->id,
                                    'negation' => $eachNonColorDetails->negation,
                                    'pre_constraint' => $eachNonColorDetails->pre_constraint,
                                    'main_value' => $eachNonColorDetails->main_value,
                                    'post_constraint' => $eachNonColorDetails->post_constraint,
                                ]);

                                $otherNonColorDetail->save();
                            }
                        }

                    }
                }
            }
        }


        $returnValues = $this->getValuesByCharacter();

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
}
