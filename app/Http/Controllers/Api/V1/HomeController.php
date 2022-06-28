<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\MyEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use App\StandardCharacter;
use App\DefaultCharacter;
use App\Character;
use App\UserTag;
use App\User;
use App\Header;
use App\Value;
use App\ColorDetails;
use App\NonColorDetails;
use App\Matrix;
use App\CharacterValue;
use DB;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

use pietercolpaert\hardf\TriGWriter;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

function startsWith($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

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
            'setNonColorValueIRI',
            'setColorBrightnessIRI',
            'setColorReflectanceIRI',
            'setColorSaturationIRI',
            'setColorColoredIRI',
            'setColorMultiColoredIRI',
            'resolveCharacter',
            'resolveNonColorValue',
            'resolveColorBrightness',
            'resolveColorReflectance',
            'resolveColorSaturation',
            'resolveColorColored',
            'resolveColorMultiColored',
            'nameMatrix',
            'getMatrixNames',
            'importMatrix',
            'overwriteMatrix',
            'getTaxons',
            'resetSystem',
            'newMatrix'
        ]);
    }

    public function getHeaders()
    {
        $headers = DB::table('headers')->where('user_id', Auth::id())
            ->orWhere('user_id', NULL)
            ->orderBy('header', 'desc')->get();
        $headers = json_decode($headers,true);
        $beforeResult = array();
        foreach ($headers as $header) {
            array_push($beforeResult, $header['header']);
        }
        natsort($beforeResult);
        $result = array();
        foreach ($beforeResult as $eachResult) {
            foreach ($headers as $header) {
                if ($eachResult == $header['header']) {
                    array_push($result, $header);
                }
            }
        }

        return array_reverse($result, false);
    }

    public function getHeadersByUserId($userID)
    {
        $headers = DB::table('headers')->where('user_id', $userID)
            ->orWhere('user_id', NULL)
            ->orderBy('header', 'desc')->get();
        $headers = json_decode($headers,true);
        $beforeResult = array();
        foreach ($headers as $header) {
            array_push($beforeResult, $header['header']);
        }
        natsort($beforeResult);
        $result = array();
        foreach ($beforeResult as $eachResult) {
            foreach ($headers as $header) {
                if ($eachResult == $header['header']) {
                    array_push($result, $header);
                }
            }
        }
        return array_reverse($result, false);
    }

    public function getAllDetails()
    {

        $colorValues = DB::table('color_details')->get();
        $nonColorValues = DB::table('non_color_details')->get();

        $data = [
            'colorValues' => $colorValues,
            'nonColorValues' => $nonColorValues,
        ];

        return $data;
    }

    public function getValuesByCharacter1($userID)
    {
        $user = User::where('id', '=', $userID)->first();
        $username = explode('@', $user['email'])[0];

        $allCharacters = DB::table('characters')->where('owner_name', '=', $username)->orderBy('order', 'ASC')->get();
    
        $headers = $this->getHeadersByUserId($userID);
        $values = DB::table('values')->join('characters', 'characters.id', '=', 'values.character_id')->where('characters.owner_name', '=', $username)->select('values.id as id', 'values.character_id as character_id', 'values.header_id as header_id', 'values.value', 'characters.unit as unit', 'characters.summary as summary')->get();
        $colorDetails = DB::table('color_details')->join('values', 'values.id', '=', 'color_details.value_id')->join('characters', 'characters.id', '=', 'values.character_id')->where('characters.owner_name', '=', $username)->get();
        $nonColorDetails = DB::table('non_color_details')->join('values', 'values.id', '=', 'non_color_details.value_id')->join('characters', 'characters.id', '=', 'values.character_id')->where('characters.owner_name', '=', $username)->get();

        $characters = [];
        $valueFlag = [];
        $character_id = [];
        $header_id = [];
        $value_id = [];
        $char_count = 0;
        $i = 0;
        foreach ($headers as $header) {
            $header_id[$header['id']] = $i;
            $i = $i + 1;
        }
        foreach ($allCharacters as $ac) {
            $character_id[$ac->id] = $char_count;
            $characters[$char_count] = [];
            for ($j = 0; $j < $i; $j++) {
                array_push($characters[$char_count], []);
            }
            $char_count = $char_count + 1;
        }

        foreach ($values as $val) {
            //$val->value = substr($val->value, 0, -1);
            if (strlen($val->value) == 0) {
                $val->value = '';
            }
            $characters[$character_id[$val->character_id]][$header_id[$val->header_id]] = $val;
            $value_id[$val->id] = 1;
        }

        foreach ($colorDetails as $col) {
            if ($value_id[$col->value_id]) {
                if ($value_id[$col->value_id] == 1) {
                    $value_id[$col->value_id] = 2;
                    if ($characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->header_id != 1) {
                        $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value = '';
                    }
                }
                if ($characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->header_id != 1) {
                    $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value = $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value . ($col->negation ? ($col->negation . ' ') : '') .
                        ($col->pre_constraint ? ($col->pre_constraint . ' ') : '') .
                        ($col->certainty_constraint ? ($col->certainty_constraint . ' ') : '') .
                        ($col->degree_constraint ? ($col->degree_constraint . ' ') : '') .
                        ($col->brightness ? ($col->brightness . ' ') : '') .
                        ($col->reflectance ? ($col->reflectance . ' ') : '') .
                        ($col->saturation ? ($col->saturation . ' ') : '') .
                        ($col->colored ? ($col->colored . ' ') : '') .
                        ($col->multi_colored ? ($col->multi_colored . ' ') : '') .
                        ($col->post_constraint ? ($col->post_constraint . ' ') : '');
                    if ($characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value != '') {
                        $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value = substr($characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value, 0, -1);
                        $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value = $characters[$character_id[$col->character_id]][$header_id[$col->header_id]]->value . '; ';
                    }
                }
            }
        }

        foreach ($nonColorDetails as $nonCol) {
            if ($value_id[$nonCol->value_id]) {
                if ($value_id[$nonCol->value_id] == 1) {
                    $value_id[$nonCol->value_id] = 2;
                    if ($characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->header_id != 1) {
                        $characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->value = '';
                    }
                }
                if ($characters[$character_id[$nonCol->character_id]][$header_id[$nonCol->header_id]]->header_id != 1) {
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
        }

        // event(new MyEvent());

        return $characters;
    }

    public function getValuesByCharacter()
    {
        return $this->getValuesByCharacter1(Auth::id());
    }

    public function checkNumericalCharacter($str)
    {
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

    public function getArrayCharacters()
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $arrayCharacters = [];

        $arrayCharacters = Character::where('owner_name', '=', $username)->orderBy('order', 'asc')->get();
        $usageCounts = [];
        $usageCounts = Character::where('owner_name', '=', $username)->join('values', 'values.character_id', '=', 'characters.id')->where('header_id', '>=', 2)->where('value', '<>', '')->select('characters.id', DB::raw('count(values.id) as usageCount'))->groupBy('characters.id')->get();

        foreach ($usageCounts as $uc) {
            foreach ($arrayCharacters as $ac) {
                if ($ac->id == $uc->id) {
                    $ac->usageCount = $uc->usageCount;
                    break;
                }
            }
        }
        return $arrayCharacters;
    }

    public function getDefaultCharacters()
    {
        $standardCharacters = DB::table('characters')->where('standard', '=', 1)->get()->toArray();
        $dfCharacters = DB::table('default_characters')->get();
        $userUsages = DB::select(DB::raw('SELECT A.name AS name, SUM(B.usage_count) AS usage_count
                                    FROM default_characters AS A
                                    INNER JOIN characters AS B ON A.name COLLATE utf8mb4_general_ci = B.name
                                    WHERE A.username = B.username
                                    GROUP BY A.name'));
        foreach ($userUsages as $uu) {
            foreach ($dfCharacters as $uc) {
                if ($uc->name == $uu->name) {
                    $uc->usage_count = $uu->usage_count;
                    break;
                }
            }
        }
        $dfCharacters = $dfCharacters->toArray();
        $defaultCharacters = array_merge($standardCharacters, $dfCharacters);

        return $defaultCharacters;
    }

    public function removeString($string, $compareStr)
    {
        $string = str_replace($compareStr, '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9, \-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }


    public function getStandardCharacters()
    {
        $result = $this->getDefaultCharacters();

        return $result;
    }

    public function getUserTags(Request $request, $userId)
    {
        $userTags = UserTag::where('user_id', '=', $userId)->get();

        return $userTags;
    }

    public function createUserTag(Request $request)
    {
        $userTag = new UserTag([
            'tag_name' => $request->input('user_tag'),
            'user_id' => $request->input('user_id')
        ]);
        $userTag->save();

        return $userTag;
    }

    public function removeUserTag(Request $request)
    {
        $userTag = UserTag::where([
            ['tag_name', '=', $request->input('user_tag')],
            ['user_id', '=', $request->input('user_id')],
        ])->delete();

        $userTags = UserTag::where('user_id', '=', $request->input('user_id'))->get();
        return $userTags;
    }

    public function deleteHeader(Request $request, $headerId)
    {

        $valuesArray = Value::where('header_id', '=', $headerId)->get();
        foreach ($valuesArray as $eachValue) {
            $character = Character::where('id', '=', $eachValue->character_id)->first();
            if ($eachValue->value ||
                ColorDetails::where('value_id', '=', $eachValue['id'])->first() ||
                NonColorDetails::where('value_id', '=', $eachValue['id'])->first()
            ) {
                if ($character->usage_count != 0) {
                    $character->usage_count = $character->usage_count - 1;
                    $character->save();
                }
            }
        }

        $headers = Header::where('id', '=', $headerId)->delete();
        $values = Value::where('header_id', '=', $headerId)->delete();

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function changeTaxon(Request $request, $taxon)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $user->taxon = $taxon;
        $user->save();

        $data = [
            'taxon' => $user->taxon
        ];

        return $data;
    }

    public function storeCharacter(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        $allImageName = array();
        if(isset($request->temp_files) && count($request->temp_files) > 0) {
            foreach ($request->temp_files as $key => $img) {
                $base64Image = explode(";base64,", $img);
                $explodeImage = explode("image/", $base64Image[0]);
                $imageType = $explodeImage[1];
                $image_base64 = base64_decode($base64Image[1]);
                $imageName = date('Y-m-d').'_'.$key.'_'.time().'.'.$imageType;
                Storage::disk('public')->put($imageName, $image_base64);
                $allImageName[] = $imageName;
            }
        }

        $character = new Character([
            'name' => $request->input('name'),
            'IRI' => $request->input('IRI'),
            'parent_term' => $request->input('parent_term'),
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
            'images' => json_encode($allImageName),
        ]);

        $character->save();

        if (DefaultCharacter::where('name', '=', $request->input('name'))->count() == 0) {
            $defaultCharacter = new DefaultCharacter([
                'name' => $request->input('name'),
                'IRI' => $request->input('IRI'),
                'parent_term' => $request->input('parent_term'),
                'method_from' => $request->input('method_from'),
                'method_to' => $request->input('method_to'),
                'method_include' => $request->input('method_include'),
                'method_exclude' => $request->input('method_exclude'),
                'method_where' => $request->input('method_where'),
                'method_as' => $request->input('method_as'),
                'unit' => $request->input('unit'),
                'standard' => 0,
                'creator' => $request->input('creator'),
                'username' => $request->input('username'),
                'owner_name' => $username,
                'usage_count' => 0,
                'show_flag' => $request->input('show_flag'),
                'standard_tag' => $request->input('standard_tag'),
                'summary' => $request->input('summary'),
                'images' => json_encode($allImageName),
            ]);

            $defaultCharacter->save();
            
        }else {
            /*$defaultCharacter = DefaultCharacter::where('id', '=', $request->input('id'))->first();
            if(!empty($allImageName) && !empty($defaultCharacter)) {
                if(!empty($defaultCharacter->images)) {
                    $img = json_decode($defaultCharacter->images,true);
                    $final_array = array_merge($img,$allImageName);
                    $character->images = json_encode($final_array);
                }else {
                    $character->images = json_encode($allImageName);
                }
            }else {
                $character->images = json_encode($allImageName); 
            } */
        }

        
        $character->images = json_encode($allImageName); 
        $character->order = $character->id;
        $character->save();
        if(!empty($request->input('third_character'))){
          $newEntry = $request->input('third_character');
          foreach ($newEntry as $ekey => $entry) {
            $checkValue = CharacterValue::where('value',$entry)->count();
            if($checkValue == 0) {
              $addvalue = new CharacterValue;
              $addvalue->value = $entry;
              $addvalue->save();
            }
          }   
        }
        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'defaultCharacters' => $returnDefaultCharacters
        ];

        //event(new MyEvent());

        return $data;

    }

    public function getCharacter(Request $request, $userId)
    {
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
        $returnAllDetailValues = $this->getAllDetails();

        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'taxon' => $returnTaxon,
            'lastMatrix' => $user['last_matrix']
        ];

        return $data;
    }

    public function deleteCharacter(Request $request, $userId, $characterId)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        $character = Character::where('id', '=', $characterId)->first();
        if (Character::where('name', '=', $character->name)->count() < 2) {
            DefaultCharacter::where('name', '=', $character->name)->delete();
        }
        if (Character::where('standard_tag', '=', Character::where('id', '=', $characterId)->first()->standard_tag)
                ->where('owner_name', '=', $username)
                ->count() < 2) {
            UserTag::where('tag_name', '=', Character::where('id', '=', $characterId)->first()->standard_tag)
                ->where('user_id', '=', Auth::id())
                ->delete();
        }


        Character::where('id', '=', $characterId)->delete();
        if (Value::where('character_id', '=', $characterId)->first()) {
            Value::where('character_id', '=', $characterId)->delete();
        }


//        $characters = Character::where('owner_name', '=', $username)->orderBy('standard_tag', 'ASC')->get();
        $characters = DB::table('characters')->where('owner_name', '=', $username)->get();

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
        $returnUserTags = DB::table('user_tags')->where('user_id', '=', Auth::id())->get();
        $returnAllDetailValues = $this->getAllDetails();
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
    
    // delete non select values during add matrix
    public function multipleDeleteCharacter(Request $request)
    {
        $ids = $request->all();
        if(!empty($ids)) {
            $user = User::where('id', '=', Auth::id())->first();
            $username = explode('@', $user['email'])[0];
            $characters = Character::whereIn('id', $ids)->get();
            foreach ($characters as $key => $character) {
                if (Character::where('name', '=', $character->name)->count() < 2) {
                    DefaultCharacter::where('name', '=', $character->name)->delete();
                }
                if (Character::where('standard_tag', '=', Character::where('id', '=', $character->id)->first()->standard_tag)
                        ->where('owner_name', '=', $username)
                        ->count() < 2) {
                    UserTag::where('tag_name', '=', Character::where('id', '=', $character->id)->first()->standard_tag)
                        ->where('user_id', '=', Auth::id())
                        ->delete();
                }
                Character::where('id', '=', $character->id)->delete();
                if (Value::where('character_id', '=', $character->id)->first()) {
                    Value::where('character_id', '=', $character->id)->delete();
                }
            }

            $characters = Character::where('owner_name', '=', $username)->get();

            $usedCharacters = Character::where('owner_name', '=', $username)->get();
            foreach ($usedCharacters as $usedCharacter) {
                $usedCharacter->username = str_replace($username . ', ', '', $usedCharacter->username);
                $usedCharacter->save();
            }

            if (!Character::where('owner_name', '=', $username)->first()) {
                Header::where('user_id', '=', Auth::id())->delete();
            }
        }
        

        $defaultCharacters = $this->getDefaultCharacters();
        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnUserTags = UserTag::where('user_id', '=', Auth::id())->get();
        $returnAllDetailValues = $this->getAllDetails();
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

    public function storeMatrix(Request $request)
    {
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
                while ($flag) {
                    $flag = false;
                    foreach ($headers as $header) {
                        if ($header['header'] == 'Sample' . $sampleNum) {
                            $sampleNum = $sampleNum + 1;
                            $flag = true;
                        }
                    }
                }
                $header = Header::create([
                    'user_id' => $request->input('user_id'),
                    'header' => 'Sample' . ($sampleNum),
                ]);
                $sampleNum = $sampleNum + 1;
            }
        }

        $headers = Header::where('user_id', '=', $request->input('user_id'))->get();

        $headerValues = Value::where('header_id', '=', 1)->get();
        $values = Value::join('headers', 'headers.id', '=', 'values.header_id')->where('headers.user_id', '=', $request->input('user_id'))->get();
        $temp = [];
        $valueFlag = [];
        foreach ($characters as $eachCharacter) {
            $valueFlag[$eachCharacter->id] = [];
            foreach ($headers as $header) {
                $valueFlag[$eachCharacter->id][$header->id] = 1;
            }
        }
        foreach ($values as $val) {
            $valueFlag[$val->character_id][$val->header_id] = 0;
        }
        foreach ($characters as $eachCharacter) {
            $flag = true;
            foreach ($headerValues as $hv) {
                if ($hv['character_id'] == $eachCharacter->id) {
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                // Value::create([
                //     'character_id' => $eachCharacter->id,
                //     'header_id' => 1,
                //     'value' => $eachCharacter->name,
                // ]);
                array_push($temp, [
                    'character_id' => $eachCharacter->id,
                    'header_id' => 1,
                    'value' => $eachCharacter->name,
                ]);

            }
            foreach ($headers as $header) {
                if ($valueFlag[$eachCharacter->id][$header->id]) {
                    // Value::create([
                    //     'character_id' => $eachCharacter->id,
                    //     'header_id' => $header->id,
                    //     'value' => '',
                    // ]);
                    array_push($temp, [
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
        $returnAllDetailValues = $this->getAllDetails();
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

    public function addMoreColumn(Request $request, $columnCount)
    {
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

        $returnAllDetailValues = $this->getAllDetails();
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

    public function addCharacter(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        $allImageName = array();
        if(isset($request->temp_files) && count($request->temp_files) > 0) {
          foreach ($request->temp_files as $key => $img) {
              $base64Image = explode(";base64,", $img);
              $explodeImage = explode("image/", $base64Image[0]);
              $imageType = $explodeImage[1];
              $image_base64 = base64_decode($base64Image[1]);
              $imageName = date('Y-m-d').'_'.$key.'_'.time().'.'.$imageType;
              Storage::disk('public')->put($imageName, $image_base64);
              $allImageName[] = $imageName;
          }
        }
        $character = new Character([
            'name' => $request->input('name'),
            'IRI' => $request->input('IRI'),
            'parent_term' => $request->input('parent_term'),
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
            'images' => json_encode($allImageName),
        ]);
        $character->save();
        if (DefaultCharacter::where('name', '=', $request->input('name'))->count() == 0) {
            $defaultCharacter = new DefaultCharacter([
                'name' => $request->input('name'),
                'IRI' => $request->input('IRI'),
                'parent_term' => $request->input('parent_term'),
                'method_from' => $request->input('method_from'),
                'method_to' => $request->input('method_to'),
                'method_include' => $request->input('method_include'),
                'method_exclude' => $request->input('method_exclude'),
                'method_where' => $request->input('method_where'),
                'method_as' => $request->input('method_as'),
                'unit' => $request->input('unit'),
                'standard' => 0,
                'creator' => $request->input('creator'),
                'username' => $request->input('username'),
                'owner_name' => $username,
                'usage_count' => 0,
                'show_flag' => $request->input('show_flag'),
                'standard_tag' => $request->input('standard_tag'),
                'summary' => $request->input('summary'),
                'images' => json_encode($allImageName),
            ]);
            $defaultCharacter->save();
        }else {
            /*$defaultCharacter = DefaultCharacter::where('id', '=', $request->input('id'))->first();
            if(!empty($allImageName) && !empty($defaultCharacter)) {
                if(!empty($defaultCharacter->images)) {
                    $img = json_decode($defaultCharacter->images,true);
                    $final_array = array_merge($img,$allImageName);
                    $character->images = json_encode($final_array);
                }else {
                    $character->images = json_encode($allImageName);
                }
            }else {
                $character->images = json_encode($allImageName); 
            } */
        }
        $character->images = json_encode($allImageName); 
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
        if(!empty($request->input('third_character'))){
          $newEntry = $request->input('third_character');
          foreach ($newEntry as $ekey => $entry) {
            $checkValue = CharacterValue::where('value',$entry)->count();
            if($checkValue == 0) {
              $addvalue = new CharacterValue;
              $addvalue->value = $entry;
              $addvalue->save();
            }
          }   
        }
        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnTaxon = $user->taxon;
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'taxon' => $returnTaxon,
            'defaultCharacters' => $returnDefaultCharacters,
        ];

        //event(new MyEvent());

        return $data;
    }

    public function updateValue(Request $request)
    {
        $value = Value::where('id', '=', $request->input('id'))->first();

        $oldValue = $value->value;
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

        $valueCount = DB::table('values')->where('character_id', '=', $character->id)
            ->where('value', '<>', '')
            ->where('value', '<>', null)
            ->where('value', '<>', $character->name)
            ->count();
        if ($valueCount > 0) {
            $character->usage_count = 1;
        } else {
            $character->usage_count = 0;
        }
        $character->save();

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnAllDetailValues = $this->getAllDetails();
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

    public function addStandardCharacter(Request $request)
    {
        $standardCharacters = $request->all();
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $order = Character::max('order') + 1;
        $characters = Character::where('owner_name', '=', $username)->select('name', 'IRI', 'method_from', 'method_to', 'method_include', 'method_exclude', 'method_where', 'owner_name','standard_tag')->get();
        Character::where('owner_name', '=', $username)/*->where('standard_tag','inflorescence')->orWhere('standard_tag','inflorescence unit')*/->delete();
        $userTags = UserTag::where('user_id', '=', Auth::id())->get();
        $newCharacters = [];
        $newUserTags = [];

        $allChKeys = ['name',
            'IRI',
            'parent_term',
            'method_from',
            'method_to',
            'method_include',
            'method_exclude',
            'method_where',
            'method_as',
            'elucidation',
            'creator',
            'unit',
            'standard',
            'username',
            'owner_name',
            'standard_tag',
            'summary',
            'usage_count',
            'order',
            'show_flag',
        ];
        foreach ($standardCharacters as $eachCharacter) {
            $flag = true;
            foreach ($characters as $ch) {
                if(isset($eachCharacter['name']) && isset($eachCharacter['IRI']) && isset($eachCharacter->method_from) && isset($eachCharacter->method_to) && isset($eachCharacter->method_include) && isset($eachCharacter->method_exclude) && isset($eachCharacter->method_where) && isset($eachCharacter->method_as)){
                    if ($eachCharacter['name'] == $ch['name']
                        && $eachCharacter['IRI'] == $ch['IRI']
                        && $eachCharacter->method_from == $ch['method_from']
                        && $eachCharacter->method_to == $ch['method_to']
                        && $eachCharacter->method_include == $ch['method_include']
                        && $eachCharacter->method_exclude == $ch['method_exclude']
                        && $eachCharacter->method_where == $ch['method_where']
                        && $eachCharacter->method_as == $ch['method_as']) {
                        $flag = false;
                        break;
                    }
                }
             
            }
            if ($flag) {
                $stdKeys = array_keys($eachCharacter);
                $tempStdCharacter = [];
                foreach ($allChKeys as $eachChKey) {
                    if (in_array($eachChKey, $stdKeys)) {
                        $tempStdCharacter[$eachChKey] = $eachCharacter[$eachChKey];
                    } else if ($eachChKey == 'order') {
                        $tempStdCharacter[$eachChKey] = $order;
                    } else if ($eachChKey == 'usage_count') {
                        $tempStdCharacter[$eachChKey] = 0;
                    } else {
                        $tempStdCharacter[$eachChKey] = '';
                    }
                }

                array_push($newCharacters, $tempStdCharacter);

                $order = $order + 1;

                $flag = true;
                foreach ($userTags as $tag) {
                    if ($tag['tag_name'] == $eachCharacter['standard_tag']) {
                        $flag = false;
                        break;
                    }
                }
                foreach ($newUserTags as $tag) {
                    if ($tag['tag_name'] == $eachCharacter['standard_tag']) {
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    array_push($newUserTags, [
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

    public function removeAllStandard(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $st_tags = Character::where('owner_name', '=', $username)->groupBy('standard_tag')->having(DB::raw('sum(standard)'), '>', 0)->having(DB::raw('sum(standard)-count(*)'), '<', 2)->select('standard_tag')->get();
        $tags = [];
        foreach ($st_tags as $tag) {
            array_push($tags, $tag->standard_tag);
        }
        Character::where('owner_name', '=', $username)->where('standard', '=', 1)->delete();
        UserTag::where('user_id', '=', Auth::id())->whereIn('tag_name', $tags)->delete();

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
        $returnAllDetailValues = $this->getAllDetails();
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

    public function showTabCharacter(Request $request, $tabName)
    {
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

        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function removeAll(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $characters = Character::where('owner_name', '=', $username)->where('standard', '=', 0)->get();
        foreach ($characters as $eachCharacter) {
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
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function updateUnit(Request $request)
    {
        $character = Character::where('id', '=', $request->input('character_id'))->first();
        $oldUnit = $character->unit;
        if ($character->unit != $request->input('unit')) {
            $character->unit = $request->input('unit');
            $character->save();
            $values = Value::where('character_id', '=', $character->id)->where('header_id', '<>', 1)->get();
            foreach ($values as $value) {
                if ($value->value) {
                    switch ($oldUnit) {
                        case "μm":
                            switch ($request->input('unit')) {
                                case "mm":
                                    $value->value = round((float)$value->value / 1000, 2);
                                    $value->save();
                                    break;
                                case "cm":
                                    $value->value = round((float)$value->value / 10000, 2);
                                    $value->save();
                                    break;
                                case "dm":
                                    $value->value = round((float)$value->value / 100000, 2);
                                    $value->save();
                                    break;
                                case "m":
                                    $value->value = round((float)$value->value / 1000000, 2);
                                    $value->save();
                                    break;
                                default:
                                    break;
                            }
                            break;
                        case "mm":
                            switch ($request->input('unit')) {
                                case "μm":
                                    $value->value = round((float)$value->value * 1000, 2);
                                    $value->save();
                                    break;
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
                                case "μm":
                                    $value->value = round((float)$value->value * 10000, 2);
                                    $value->save();
                                    break;
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
                                case "μm":
                                    $value->value = round((float)$value->value * 100000, 2);
                                    $value->save();
                                    break;
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
                                case "μm":
                                    $value->value = round((float)$value->value * 1000000, 2);
                                    $value->save();
                                    break;
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
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function exportDescription(Request $request)
    {

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


        $userCharacters = $request->input('userCharacters');
        $headers = $request->input('headers');
        $values = $request->input('values');
        $userTags = $request->input('userTags');


        $htmlString = '<table>';

        if (count($headers) > 0) {
            $htmlString .= '<tr><th>Character</th><th>Unit</th><th>Summary</th>';
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
                                    $htmlString .= '<td>' . $eachUserCharacter['unit'] . '</td>';
                                    if (substr($eachValue['value'], 0, 6) == 'Length' ||
                                        substr($eachValue['value'], 0, 5) == 'Width' ||
                                        substr($eachValue['value'], 0, 5) == 'Depth' ||
                                        substr($eachValue['value'], 0, 8) == 'Diameter' ||
                                        substr($eachValue['value'], 0, 5) == 'Count' ||
                                        substr($eachValue['value'], 0, 8) == 'Distance' ||
                                        substr($eachValue['value'], 0, 6) == 'Number' ||
                                        substr($eachValue['value'], 0, 5) == 'Ratio') {
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


        $user = User::where('id', '=', Auth::id())->first();

        $fileName = $user->taxon;
        $file = fopen($fileName . ".trig", "w") or die("Unable to open file!");

        $taxons = [];
        $result = $this->getTrigDescription(Auth::id(), $taxons)['description'];

        fwrite($file, $result);

        fclose($file);


        $zipper = new \Chumper\Zipper\Zipper;

        $zipper->make($fileName . '.zip')->folder($fileName)->add(array(
            $fileName . '.docx',
            $fileName . '.csv',
            $fileName . '.trig'
        ));

        $zipper->close();

        return array(
            'is_success' => 1,
            'doc_url' => config('constants.root_path') . $fileName . '.zip',
        );
    }

    public function exportDescriptionCsv(Request $request)
    {
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
            'is_success' => 1,
            'doc_url' => config('constants.root_path') . $fileName . '.csv',
        );
    }


    public function calcSummary($arrayRow)
    {
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

    public function updateSummary(Request $request)
    {
        $character = Character::where('id', '=', $request->input('character_id'))->first();
        $character->summary = $request->input('summary');
        $character->save();

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }
    
    // check image and size
    public function checkImage(Request $request) {
        $validatedData = validator($request->all(),['image' => 'required|mimes:jpeg,jpg,png|max:2048']);
        if($validatedData->fails()){
            return $validatedData->errors()->first();
        }else {
            return 1;
        }
    }

    // identify character
    public function identify(Request $request) {
        $data =$request->all();
        $info1 = Character::where('name',$data[0])->where('owner_name',$data[1])/*->where('id',$data[2])*/->first();
        $info2 = defaultCharacter::where('name',$data[0])->where('owner_name','!=',$data[1])->first();
        $mainImage = array();
        $uploadedImage = array();
        if(!empty($info2) && !empty($info2->images)) {
          $mainImage = json_decode($info2->images,true);
        }
        if(!empty($info1) && !empty($info1->images)) {
          $uploadedImage = json_decode($info1->images,true);
        }
        return array_merge($mainImage,$uploadedImage);
    }

    public function editView(Request $request) {
        $data =$request->all();
        $info1 = Character::where('name',$data[0])->where('owner_name',$data[1])->where('images','!=','')->first();
       // $info2 = defaultCharacter::where('name',$data[0])->where('owner_name','!=',$data[1])->first();
        $mainImage = array();
        $uploadedImage = array();
       /* if(!empty($info2) && !empty($info2->images)) {
          $mainImage = json_decode($info2->images,true);
        }*/
        if(!empty($info1) && !empty($info1->images)) {
          $uploadedImage = json_decode($info1->images,true);
        }
        return array_merge($mainImage,$uploadedImage);
    }

    // update image, if character is already used.
    public function updateImage(Request $request) {
      $data = $request->all();
      $allImageName = array();
      if(isset($request->temp_files) && count($request->temp_files) > 0) {
        foreach ($request->temp_files as $key => $img) {
            $base64Image = explode(";base64,", $img);
            $explodeImage = explode("image/", $base64Image[0]);
            $imageType = $explodeImage[1];
            $image_base64 = base64_decode($base64Image[1]);
            $imageName = date('Y-m-d').'_'.$key.'_'.time().'.'.$imageType;
            Storage::disk('public')->put($imageName, $image_base64);
            $allImageName[] = $imageName;
        }
        $info1 = Character::where('name',$request->input('name'))->where('owner_name',$request->other_name)->orderBy('id','desc')->first();
        if(!empty($info1)) {
          $prevImages = array();
          if(!empty($info1->images)) {
            $prevImages = json_decode($info1->images,true);
          }
          $info1->images = json_encode(array_merge($prevImages,$allImageName));
          $info1->save();
        }
        
        $info2 = DefaultCharacter::where('name', '=', $request->input('name'))->where('owner_name',$request->other_name)->where('username',$request->owner_name)->first();
        if(!empty($info2)) {
          $oldImages = array();
          if(!empty($info2->images)) {
            $oldImages = json_decode($info2->images,true);
          }
          $info2->images = json_encode(array_merge($oldImages,$allImageName));
          $info2->save();
        } 
      }
    }

    public function updateCharacter(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $character = Character::where('id', '=', $request->input('id'))->first();

        $oldTag = null;
        if ($character) {
            $oldTag = $character->standard_tag;
        } else {
            $character = new Character();
        }
        if(isset($request->temp_files) && count($request->temp_files) > 0) {
            foreach ($request->temp_files as $key => $img) {
                $base64Image = explode(";base64,", $img);
                $explodeImage = explode("image/", $base64Image[0]);
                $imageType = $explodeImage[1];
                $image_base64 = base64_decode($base64Image[1]);
                $imageName = date('Y-m-d').'_'.$key.'_'.time().'.'.$imageType;
                Storage::disk('public')->put($imageName, $image_base64);
                $allImageName[] = $imageName;
            }
        }

        $character->name = $request->input('name');
        $character->parent_term = $request->input('parent_term');
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
        $character->owner_name = $username;
        $character->usage_count = $request->input('usage_count');
        $character->show_flag = $request->input('show_flag');
        $character->standard_tag = $request->input('standard_tag');
        $character->summary = $request->input('summary');
        if(!empty($allImageName)) {
           if(!empty($character->images)) {
                $img = json_decode($character->images,true);
                $final_array = array_merge($img,$allImageName);
                $character->images = json_encode($final_array);
            }else {
                $character->images = json_encode($allImageName);
            } 
        }
        
        $character->save();

        if ($oldTag) {
            if ($oldTag != $character->standard_tag) {
                if (Character::where('owner_name', '=', $username)->where('standard_tag', '=', $oldTag)->first() == null) {
                    UserTag::where('tag_name', '=', $oldTag)->where('user_id', '=', Auth::id())->delete();
                }

                if (UserTag::where('tag_name', '=', $character->standard_tag)->where('user_id', '=', Auth::id())->first() == null) {
                    $userTag = new UserTag([
                        'tag_name' => $character->standard_tag,
                        'user_id' => Auth::id()
                    ]);

                    $userTag->save();
                }
            }
        }

        $returnUserTags = UserTag::where('user_id', '=', Auth::id())->get();
        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'defaultCharacters' => $returnDefaultCharacters,
            'userTags' => $returnUserTags
        ];

        return $data;
    }

    public function changeOrder(Request $request)
    {
        $firstId = $request->input('firstId');
        $secondId = $request->input('secondId');

        $firstCharacter = Character::where('id', '=', $firstId)->first();
        $secondCharacter = Character::where('id', '=', $secondId)->first();

        $tmp = $firstCharacter->order;
        $firstCharacter->order = $secondCharacter->order;
        $secondCharacter->order = $tmp;

        $firstCharacter->save();
        $secondCharacter->save();

        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function updateHeader(Request $request)
    {
        $header = Header::where('id', '=', $request->input('id'))->first();
        $header->header = $request->input('header');
        $header->save();
        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();

        $data = [
            'headers' => $returnHeaders,
            'values' => $returnValues,
        ];

        return $data;
    }

    public function getUsage(Request $request, $characterId)
    {
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

    public function getColorDetails(Request $request, $valueId)
    {
        $colorDetails = ColorDetails::where('value_id', '=', $valueId)->get();
        $returnValues = $this->getValuesByCharacter();
        $user = User::where('id', '=', Auth::id())->first();

        $selectedCharacter = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first();
        $users = array();
        if(isset($user->taxon) && !empty($user->taxon)) {
          $users = User::where('taxon', '=', $user->taxon)->pluck('email')->toArray();
        }
        $username = array();
        if(count($users) > 0) {
            foreach ($users as $key => $value) {
                $username[] = explode('@', $value)[0];
            }
        }
        $characterList = Character::where('name', '=', $selectedCharacter->name)->whereIn('owner_name',$username)->get()->toArray();

        $existDetails = [];

        foreach ($characterList as $eachCharacter) {
            $values = Value::where('character_id', '=', $eachCharacter['id'])->get();
            foreach ($values as $eachValue) {
                $existColorDetails = ColorDetails::where('value_id', '=', $eachValue->id)->get()->toArray();
                foreach ($existColorDetails as $eachColorDetails) {
                    $eachColorDetails['username'] = $eachCharacter['owner_name'];

                    array_push($existDetails, $eachColorDetails);
                }
            }
        }

        $data = [
            'colorDetails' => $colorDetails,
            'values' => $returnValues,
            'existColorDetails' => $existDetails,
        ];

        return $data;
    }

    public function saveColorValue(Request $request)
    {
        $colorValues = $request->all();
        $id = 0;
        Value::where('id', '=', $request->input('value_id'))->where('header_id', '!=', 1)->update(['value'=>'']);
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
            $id = $request->input('id');
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
            $id = $color->id;
        }

        $character = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first();
        $character->usage_count = 1;
        $character->save();

//        $characterValues = Value::where('character_id', '=', $character->id)->get()->toArray();
//
//        $tempCount = 0;

//        foreach ($characterValues as $eachValue) {
//            if (ColorDetails::where('value_id', '=', $eachValue['id'])->count() == 1) {
//                $tempCount++;
//            }
//        }

//        if ($tempCount == 1) {
//            $character->usage_count = $character->usage_count + 1;
//            $character->save();
//        }

        $returnColorDetails = ColorDetails::where('value_id', '=', $request->input('value_id'))->get();
//        $characters = Character::where('name', '=', $characterName)->get();

        $constraints = $this->getDefaultConstraint($character->name);

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllDetails();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $data = [
            'id' => $id,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
            'colorDetails' => $returnColorDetails,
            'defaultCharacters' => $returnDefaultCharacters
        ];

        return $data;
    }

    public function getConstraint(Request $request, $characterName)
    {
        $characters = Character::where('name', '=', $characterName)->get();

        $preList = ['longitudinally'];
        $postList = ['when young'];
        foreach ($characters as $eachCharacter) {
            $values = Value::where('character_id', '=', $eachCharacter->id)->where('header_id', '<>', 1)->get();
            foreach ($values as $eachValue) {
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

        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $preList,
            'postList' => $postList,
        ];

        return $data;
    }

    public function getNonColorDetails(Request $request, $valueId)
    {
        $nonColorDetails = NonColorDetails::where('value_id', '=', $valueId)->get();
        $returnValues = $this->getValuesByCharacter();
        $user = User::where('id', '=', Auth::id())->first();
        $selectedCharacter = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first();
        $users = array();
        if(isset($user->taxon) && !empty($user->taxon)) {
          $users = User::where('taxon', '=', $user->taxon)->pluck('email')->toArray();
        }
        $username = array();
        if(count($users) > 0) {
            foreach ($users as $key => $value) {
                $username[] = explode('@', $value)[0];
            }
        }

        $characterList = Character::where('name', '=', $selectedCharacter->name)->whereIn('owner_name',$username)->get()->toArray();

        $existDetails = [];

        foreach ($characterList as $eachCharacter) {
            $values = Value::where('character_id', '=', $eachCharacter['id'])->get();
            foreach ($values as $eachValue) {
                $existNonColorDetails = NonColorDetails::where('value_id', '=', $eachValue->id)->get()->toArray();
                foreach ($existNonColorDetails as $eachNonColorDetails) {
                    $eachNonColorDetails['username'] = $eachCharacter['owner_name'];
                    array_push($existDetails, $eachNonColorDetails);
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

    public function saveNonColorValue(Request $request)
    {
        $nonColorValues = $request->all();
        $nonColorDetails = NonColorDetails::where('id', '=', $request->input('id'))->first();
        $id = 0;
        Value::where('id', '=', $request->input('value_id'))->where('header_id', '!=', 1)->update(['value'=>'']);
        if ($request->input('id') && $nonColorDetails) {
            $nonColorDetails->value_id = $request->input('value_id');
            $nonColorDetails->negation = $request->input('negation');
            $nonColorDetails->pre_constraint = $request->input('pre_constraint');
            $nonColorDetails->certainty_constraint = $request->input('certainty_constraint');
            $nonColorDetails->degree_constraint = $request->input('degree_constraint');
            $nonColorDetails->main_value = $request->input('main_value');
            $nonColorDetails->main_value_IRI = $request->input('main_value_IRI');
            $nonColorDetails->post_constraint = $request->input('post_constraint');

            $nonColorDetails->save();
            $id = $request->input('id');
        } else {
            if (Value::where('id', '=', $request->input('value_id'))->first()->header_id != 1) {
                $nonColorDetails = new NonColorDetails([
                    'value_id' => $request->input('value_id'),
                    'negation' => $request->input('negation') ? $request->input('negation') : null,
                    'pre_constraint' => $request->input('pre_constraint') ? $request->input('pre_constraint') : null,
                    'certainty_constraint' => $request->input('certainty_constraint') ? $request->input('certainty_constraint') : null,
                    'degree_constraint' => $request->input('degree_constraint') ? $request->input('degree_constraint') : null,
                    'main_value' => $request->input('main_value') ? $request->input('main_value') : null,
                    'main_value_IRI' => $request->input('main_value_IRI') ? $request->input('main_value_IRI') : null,
                    'post_constraint' => $request->input('post_constraint') ? $request->input('post_constraint') : null,
                ]);

                $nonColorDetails->save();

                $id = $nonColorDetails->id;
            }
        }

        $character = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first();
        $character->usage_count = 1;
        $character->save();

//        $characterValues = Value::where('character_id', '=', $character->id)->get()->toArray();
//
//        $tempCount = 0;

//        foreach ($characterValues as $eachValue) {
//            if (NonColorDetails::where('value_id', '=', $eachValue['id'])->first()) {
//                $tempCount++;
//            }
//        }
//
//        if ($tempCount == 1) {
//            $character->usage_count = $character->usage_count + 1;
//            $character->save();
//        }

//        $characters = Character::where('name', '=', $characterName)->get();

        $constraints = $this->getDefaultConstraint($character);

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllDetails();
        $returnNonColorDetails = DB::table('non_color_details')->where('value_id', '=', $request->input('value_id'))->get();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $data = [
            'id' => $id,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
            'nonColorDetails' => $returnNonColorDetails,
            'defaultCharacters' => $returnDefaultCharacters
        ];

        return $data;
    }

    public function getDefaultConstraint($characterName)
    {
        $characters = DB::table('characters')->where('name', 'like', $characterName)->get();

        $preList = ['longitudinally'];
        $postList = ['when young'];
        foreach ($characters as $eachCharacter) {
            $values = DB::table('values')->where('character_id', '=', $eachCharacter->id)->where('header_id', '<>', 1)->get();
            foreach ($values as $eachValue) {
                $details = DB::table('color_details')->where('value_id', '=', $eachValue->id)->get();
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
                $details = DB::table('non_color_details')->where('value_id', '=', $eachValue->id)->get();
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

    public function getDefaultConstraint1()
    {
        // get the id of values whose has relation with characters
        $values =   DB::table('characters')
                        ->leftJoin('values', 'values.character_id', '=', 'characters.id')
                        ->where('values.header_id','<>',1)
                        ->orderBy('values.id','ASC')
                        ->pluck('values.id');

        $preList = ['longitudinally'];
        $postList = ['when young'];
        if(count($values) > 0) { 
           $colors_details =  ColorDetails::whereIn('value_id',$values)->get();
           $non_colors_details =  NonColorDetails::whereIn('value_id',$values)->get();  
            if(count($colors_details) > 0){
                foreach ($colors_details as $eachColor) {
                    if ($eachColor->pre_constraint != null && $eachColor->pre_constraint != '' && $eachColor->pre_constraint != 'undefined' && $eachColor->pre_constraint != 'null') {
                        if (!in_array($eachColor->pre_constraint, $preList)) {
                            array_push($preList, $eachColor->pre_constraint);
                        }
                    }
                    if ($eachColor->post_constraint != null && $eachColor->post_constraint != '' && $eachColor->post_constraint != 'undefined' && $eachColor->post_constraint != 'null') {
                        if (!in_array($eachColor->post_constraint, $postList)) {
                            array_push($postList, $eachColor->post_constraint);
                        }
                    }
                }
            }
            if(count($non_colors_details) > 0){
                foreach ($non_colors_details as $eachNonColor) {
                    if ($eachNonColor->pre_constraint != null && $eachNonColor->pre_constraint != '' && $eachNonColor->pre_constraint != 'undefined' && $eachNonColor->pre_constraint != 'null') {
                        if (!in_array($eachNonColor->pre_constraint, $preList)) {
                            array_push($preList, $eachNonColor->pre_constraint);
                        }
                    }
                    if ($eachNonColor->post_constraint != null && $eachNonColor->post_constraint != '' && $eachNonColor->post_constraint != 'undefined' && $eachNonColor->post_constraint != 'null') {
                        if (!in_array($eachNonColor->post_constraint, $postList)) {
                            array_push($postList, $eachNonColor->post_constraint);
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

    public function removeColorValue(Request $request)
    {
        $valueId = $request->input('value_id');

        $character = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first();

        $characterValues = Value::where('character_id', '=', $character->id)->get()->toArray();

        $tempCount = 0;

        foreach ($characterValues as $eachValue) {
            if (ColorDetails::where('value_id', '=', $eachValue['id'])->count() > 0) {
                $tempCount++;
            }
        }

        if ($tempCount == 0) {
            $character->usage_count = 0;
            $character->save();
        }

        ColorDetails::where('value_id', '=', $valueId)->delete();

        $returnValues = $this->getValuesByCharacter();

        $characterName = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first()->name;

        $constraints = $this->getDefaultConstraint($characterName);

        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];
        return $data;
    }

    public function removeEachColorDetails(Request $request)
    {
        $eachColorDetails = ColorDetails::find($request->input('id'));
        if ($eachColorDetails) {
            $eachColorDetails->delete();
            $character = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first();
            $characterValues = DB::table('values')->where('character_id', '=', $character->id)->get();
            $tempCount = 0;
            foreach ($characterValues as $eachValue) {
                if (DB::table('color_details')->where('value_id', '=', $eachValue->id)->count() > 0) {
                    $tempCount++;
                }
            }
            if ($tempCount == 0) {
                $character->usage_count = 0;
                $character->save();
            }
        }

        $returnColorDetails = DB::table('color_details')->where('value_id', '=', $request->input('value_id'))->get();
        $returnValues = $this->getValuesByCharacter();
        $characterName = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first()->name;

        $constraints = $this->getDefaultConstraint($characterName);

        $returnAllDetailValues = $this->getAllDetails();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $data = [
            'colorDetails' => $returnColorDetails,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
            'defaultCharacters' => $returnDefaultCharacters
        ];

        return $data;
    }

    public function removeEachNonColorDetails(Request $request)
    {
        $eachNonColorDetails = NonColorDetails::find($request->input('id'));

        if ($eachNonColorDetails) {
            $eachNonColorDetails->delete();
            $character = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first();
            $characterValues = DB::table('values')->where('character_id', '=', $character->id)->get();
            $tempCount = 0;
            foreach ($characterValues as $eachValue) {
                if (DB::table('non_color_details')->where('value_id', '=', $eachValue->id)->count() > 0) {
                    $tempCount++;
                }
            }
            if ($tempCount == 0) {
                $character->usage_count = 0;
                $character->save();
            }
        }
        $returnValues = $this->getValuesByCharacter();

        $returnNonColorDetails = DB::table('non_color_details')->where('value_id', '=', $request->input('value_id'))->get();
        $characterName = DB::table('characters')->where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first()->name;

        $constraints = $this->getDefaultConstraint($characterName);

        $returnAllDetailValues = $this->getAllDetails();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $data = [
            'nonColorDetails' => $returnNonColorDetails,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
            'defaultCharacters' => $returnDefaultCharacters
        ];

        return $data;
    }

    public function removeNonColorValue(Request $request)
    {
        $valueId = $request->input('value_id');

        $character = Character::where('id', '=', Value::where('id', '=', $request->input('value_id'))->first()->character_id)->first();

        $characterValues = Value::where('character_id', '=', $character->id)->get()->toArray();

        $tempCount = 0;

        foreach ($characterValues as $eachValue) {
            if (NonColorDetails::where('value_id', '=', $eachValue['id'])->count() > 0) {
                $tempCount++;
            }
        }

        if ($tempCount == 0) {
            $character->usage_count = 0;
            $character->save();
        }

        NonColorDetails::where('value_id', '=', $valueId)->delete();


        $characterName = Character::where('id', '=', Value::where('id', '=', $valueId)->first()->character_id)->first()->name;

        $returnValues = $this->getValuesByCharacter();

        $constraints = $this->getDefaultConstraint($characterName);
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];

        return $data;
    }

    public function getColorValues(Request $request)
    {
        $valueIds = $request->input('value_id');

        $colorValues = ColorDetails::whereIn('value_id', $valueIds)->get();

        $data = [
            'colorValues' => $colorValues,
        ];

        return $data;
    }

    public function overwriteValue(Request $request)
    {
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
                    if ($eachValue->id != $value['id'] && $eachValue->header_id != 1) {
                        ColorDetails::where('value_id', '=', $eachValue->id)->delete();
                        foreach ($colorDetails as $eachColorDetails) {
                            $otherColorDetail = new ColorDetails([
                                'value_id' => $eachValue->id,
                                'negation' => $eachColorDetails->negation,
                                'pre_constraint' => $eachColorDetails->pre_constraint,
                                'certainty_constraint' => $eachColorDetails->certainty_constraint,
                                'degree_constraint' => $eachColorDetails->degree_constraint,
                                'brightness' => $eachColorDetails->brightness,
                                'birghtness_IRI' => $eachColorDetails->brightness_IRI,
                                'reflectance' => $eachColorDetails->reflectance,
                                'reflectance_IRI' => $eachColorDetails->reflectance_IRI,
                                'saturation' => $eachColorDetails->saturation,
                                'saturation_IRI' => $eachColorDetails->saturation_IRI,
                                'colored' => $eachColorDetails->colored,
                                'colored_IRI' => $eachColorDetails->colored_IRI,
                                'multi_colored' => $eachColorDetails->multi_colored,
                                'multi_colored_IRI' => $eachColorDetails->multi_colored_IRI,
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
                    if ($eachValue->id != $value['id'] && $eachValue->header_id != 1) {
                        NonColorDetails::where('value_id', '=', $eachValue->id)->delete();
                        foreach ($nonColorDetails as $eachNonColorDetails) {
                            $otherNonColorDetail = new NonColorDetails([
                                'value_id' => $eachValue->id,
                                'negation' => $eachNonColorDetails->negation,
                                'pre_constraint' => $eachNonColorDetails->pre_constraint,
                                'certainty_constraint' => $eachNonColorDetails->certainty_constraint,
                                'degree_constraint' => $eachNonColorDetails->degree_constraint,
                                'main_value' => $eachNonColorDetails->main_value,
                                'main_value_IRI' => $eachNonColorDetails->main_value_IRI,
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
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];

        return $data;

    }

    public function keepExistValue(Request $request)
    {
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
                    if ($eachValue->id != $value['id'] && $eachValue->header_id != 1) {
                        $existColorDetails = ColorDetails::where('value_id', '=', $eachValue->id)->first();
                        if (!$existColorDetails) {
                            foreach ($colorDetails as $eachColorDetails) {
                                $otherColorDetail = new ColorDetails([
                                    'value_id' => $eachValue->id,
                                    'negation' => $eachColorDetails->negation,
                                    'pre_constraint' => $eachColorDetails->pre_constraint,
                                    'certainty_constraint' => $eachColorDetails->certainty_constraint,
                                    'degree_constraint' => $eachColorDetails->degree_constraint,
                                    'brightness' => $eachColorDetails->brightness,
                                    'birghtness_IRI' => $eachColorDetails->brightness_IRI,
                                    'reflectance' => $eachColorDetails->reflectance,
                                    'reflectance_IRI' => $eachColorDetails->reflectance_IRI,
                                    'saturation' => $eachColorDetails->saturation,
                                    'saturation_IRI' => $eachColorDetails->saturation_IRI,
                                    'colored' => $eachColorDetails->colored,
                                    'colored_IRI' => $eachColorDetails->colored_IRI,
                                    'multi_colored' => $eachColorDetails->multi_colored,
                                    'multi_colored_IRI' => $eachColorDetails->multi_colored_IRI,
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
                    if ($eachValue->id != $value['id'] && $eachValue->header_id != 1) {
                        $existNonColorDetails = NonColorDetails::where('value_id', '=', $eachValue->id)->first();
                        if (!$existNonColorDetails) {
                            foreach ($nonColorDetails as $eachNonColorDetails) {
                                $otherNonColorDetail = new NonColorDetails([
                                    'value_id' => $eachValue->id,
                                    'negation' => $eachNonColorDetails->negation,
                                    'pre_constraint' => $eachNonColorDetails->pre_constraint,
                                    'certainty_constraint' => $eachNonColorDetails->certainty_constraint,
                                    'degree_constraint' => $eachNonColorDetails->degree_constraint,
                                    'main_value' => $eachNonColorDetails->main_value,
                                    'main_value_IRI' => $eachNonColorDetails->main_value_IRI,
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
        $returnAllDetailValues = $this->getAllDetails();
        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'preList' => $constraints['preList'],
            'postList' => $constraints['postList'],
        ];

        return $data;

    }

    public function getCharacterNameById($userID)
    {
        $user = User::where('id', '=', $userID)->first();
        $username = explode('@', $user->email)[0];

        $characters = Character::where('owner_name', '=', $username)->get();

        $returenCharacters = [];

        foreach ($characters as $char) {
            $returenCharacters[$char->id] = $char->name;
        }

        return $returenCharacters;
    }

    public function getColorDetailsById($userID)
    {
        $valueIds = Value::join('headers', 'headers.id', '=', 'values.header_id')->where('headers.user_id', '=', $userID)->select('values.id')->get();
        $ids = [];
        $returnDetails = [];
        foreach ($valueIds as $value) {
            array_push($ids, $value->id);
            $returnDetails[$value->id] = [];
        }
        $colDetails = ColorDetails::whereIn('value_id', $ids)->get();
        foreach ($colDetails as $col) {
            array_push($returnDetails[$col->value_id], $col);
        }
        return $returnDetails;
    }

    public function getNonColorDetailsById($userID)
    {
        $valueIds = Value::join('headers', 'headers.id', '=', 'values.header_id')->where('headers.user_id', '=', $userID)->select('values.id')->get();
        $ids = [];
        $returnDetails = [];
        foreach ($valueIds as $value) {
            array_push($ids, $value->id);
            $returnDetails[$value->id] = [];
        }
        $nonColDetails = NonColorDetails::whereIn('value_id', $ids)->get();
        foreach ($nonColDetails as $nonCol) {
            array_push($returnDetails[$nonCol->value_id], $nonCol);
        }
        return $returnDetails;
    }

    public function getColorDetailText($col)
    {
        $text = ($col->pre_constraint ? ($col->pre_constraint . ' ') : '') .
            ($col->certainty_constraint ? ($col->certainty_constraint . ' ') : '') .
            ($col->degree_constraint ? ($col->degree_constraint . ' ') : '') .
            ($col->brightness ? ($col->brightness . ' ') : '') .
            ($col->reflectance ? ($col->reflectance . ' ') : '') .
            ($col->saturation ? ($col->saturation . ' ') : '') .
            ($col->colored ? ($col->colored . ' ') : '') .
            ($col->multi_colored ? ($col->multi_colored . ' ') : '') .
            ($col->post_constraint ? ($col->post_constraint . ' ') : '');

        $text = substr($text, 0, strlen($text) - 1);

        return $text;
    }

    public function getNonColorDetailText($nonCol)
    {
        $text = ($nonCol->pre_constraint ? ($nonCol->pre_constraint . ' ') : '') .
            ($nonCol->certainty_constraint ? ($nonCol->certainty_constraint . ' ') : '') .
            ($nonCol->degree_constraint ? ($nonCol->degree_constraint . ' ') : '') .
            ($nonCol->main_value ? ($nonCol->main_value . ' ') : '') .
            ($nonCol->post_constraint ? ($nonCol->post_constraint . ' ') : '');

        $text = substr($text, 0, strlen($text) - 1);

        return $text;
    }

    public function writeColorDetail($writer, $subject, $col, $graph)
    {
        if ($col->colored) {
            $writer->addTriple($subject, ":has_hue_value", ":" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $col->colored)))), $graph);
        }
        if ($col->certainty_constraint) {
            $writer->addTriple($subject, ":has_certainty_value_modifier", "mo:" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $col->certainty_constraint)))), $graph);
        }
        if ($col->degree_constraint) {
            $writer->addTriple($subject, ":has_degree_value_modifier", "mo:" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $col->degree_constraint)))), $graph);
        }
        if ($col->brightness) {
            $writer->addTriple($subject, ":has_brightness_value", ":" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $col->brightness)))), $graph);
        }
        if ($col->reflectance) {
            $writer->addTriple($subject, ":has_reflectance_value", ":" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $col->reflectance)))), $graph);
        }
        if ($col->saturation) {
            $writer->addTriple($subject, ":has_saturation_value", ":" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $col->saturation)))), $graph);
        }
        if ($col->multi_colored) {
            $writer->addTriple($subject, ":has_pattern_value", ":" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $col->multi_colored)))), $graph);
        }
        if ($col->pre_constraint || $col->post_constraint) {
            $writer->addTriple($subject, ":has_value", "\"" . $this->getColorDetailText($col) . "\"", $graph);
        }
    }

    public function writeNonColorDetail($writer, $subject, $nonCol, $graph)
    {
        if ($nonCol->main_value) {
            $writer->addTriple($subject, ":has_value", ":" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $nonCol->main_value)))), $graph);
        }
        if ($nonCol->certainty_constraint) {
            $writer->addTriple($subject, ":has_certainty_value_modifier", "mo:" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $nonCol->certainty_constraint)))), $graph);
        }
        if ($nonCol->degree_constraint) {
            $writer->addTriple($subject, ":has_degree_value_modifier", "mo:" . str_replace("(", "", str_replace(")", "", str_replace(" ", "_", str_replace("-", "_", $nonCol->degree_constraint)))), $graph);
        }
        if ($nonCol->pre_constraint || $nonCol->post_constraint) {
            $writer->addTriple($subject, ":has_value", "\"" . $this->getNonColorDetailText($nonCol) . "\"", $graph);
        }
    }

    public function pluralToSingle($word)
    {
        $plurals = [
            'plants' => 'plant',
            'internodes' => 'internode',
            'rhizomes' => 'rhizome',
            'stems' => 'stem',
            'leaves' => 'leaf',
            'blades' => 'blade',
            'margins' => 'margin',
            'surface' => 'surface',
            'sheaths' => 'sheath',
            'ligules' => 'ligule',
            'apex' => 'apex',
            'scales' => 'scale',
            'veins' => 'vein',
            'stipe' => 'stipe',
            'beak' => 'beak',
            'teeth' => 'tooth',
            'perigynia' => 'perigynium',
            'stigmas' => 'stigma',
            'achenes' => 'achene',
            'anthers' => 'anther',
            'shoots' => 'shoot',
            'branches' => 'branch',
            'roots' => 'root',
            'culms' => 'culm',
            'midrib' => 'midrib',
            'bands' => 'band',
            'inflorescences' => 'inflorescence',
            'axis' => 'axis',
            'nodes' => 'node',
            'peduncles' => 'peduncle',
            'bracts' => 'bract',
            'rachis' => 'rachis',
            'spikes' => 'spike',
            'sides' => 'side',
            'styles' => 'style',
            'stamens' => 'stamen',
            'filaments' => 'filament',
            'cataphylls' => 'cataphyll',
            'flowers' => 'flower',
            'nerves' => 'nerve',
            'unit' => 'unit',
            'body' => 'body',
            'orifice' => 'orifice',
        ];
        if (array_key_exists($word, $plurals)) {
            return $plurals[$word];
        }
        if (substr($word, strlen($word) - 1) == "s") {
            return substr($word, 0, strlen($word) - 1);
        }
        return $word;
    }

    public function partNameConverter($partName)
    {
        $word = explode('_of_', explode('_in_', $partName)[0])[0];
        // return $this->pluralToSingle($word) . substr($partName, strlen($word));
        return $this->pluralToSingle($word);
    }

    public function registerType($writer, &$types, $type, $sampleName, $graph, $ccs)
    {
        $a = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
        $i = 2;
        if (!array_search($type, $types)) {
            array_push($types, $type);
            $newType = explode('_of_', explode('_in_', $type)[0])[0];
            $writer->addTriple($ccs . $type, $a, ":" . $this->partNameConverter($newType), $graph);
            while (strlen($newType) != strlen($type)) {
                $partOf = $this->partNameConverter(substr($type, strlen($newType) + 4));
                $writer->addTriple($ccs . $type, ":part_of", $ccs . $partOf, $graph);
                $type = $partOf;
                $newType = explode('_of_', explode('_in_', $type)[0])[0];
                $writer->addTriple($ccs . $type, $a, ":" . $this->partNameConverter($newType), $graph);
                $i--;
                if (!$i) return;
            }
        }
    }

    public function registerPartName($writer, &$partNames, &$types, $charName, $qualityName, $sampleName, $graph, $ccs)
    {
        $a = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
        if (strpos($qualityName, 'distance_of_') !== false) {
            $qualityName = str_replace('distance_of_', 'distance_between_', $qualityName);
        }
        if (strstr($charName, 'plant')) {
            $writer->addTriple($sampleName, ':has_quality', $ccs . $charName, $graph);
        } else {
            $partName = explode('_of_', $charName)[1];
            $partName = $this->partNameConverter($partName);
            if (!array_search($partName, $partNames)) {
                array_push($partNames, $partName);
                $writer->addTriple($sampleName, ':has_part', $ccs . $partName, $graph);
                $this->registerType($writer, $types, $partName, $sampleName, $graph, $ccs);
            }
            $writer->addTriple($ccs . $partName, ':has_quality', $ccs . $qualityName, $graph);
        }
        if (strpos($charName, 'distance_of_') !== false) {
            $charName = str_replace('distance_of_', 'distance_between_', $charName);
        }
        $writer->addTriple($ccs . $qualityName, $a, ":$charName", $graph);
    }

    public function checkHavingUnit($charName)
    {
        return startsWith($charName, 'length_of_')
            || startsWith($charName, 'width_of_')
            || startsWith($charName, 'number_of_')
            || startsWith($charName, 'depth_of_')
            || startsWith($charName, 'diameter_of_')
            || startsWith($charName, 'distance_between_')
            || startsWith($charName, 'distance_of_')
            || startsWith($charName, 'count_of_')
            || startsWith($charName, 'ratio_of_');
    }

    public function getSpecimenName($taxon)
    {
        $words = explode(' ', strtolower($taxon));
        $str = '';
        foreach ($words as $word) {
            $str .= substr($word, 0, 1);
        }

        return $str . 's';
    }

    public function getTrigDescription($userID, $taxons)
    {
        $user = User::where('id', '=', $userID)->first();
        // $txid = $request->input('id');
        $txid = '';
        if (array_key_exists($user->taxon, $taxons)) {
            $txid = $taxons[$user->taxon];
        } else {
            $txid = $this->getTaxonID($userID);
            $taxons[$user->taxon] = $txid;
        }

        $specimenName = $this->getSpecimenName($user->taxon);

        $writer = new TriGWriter([
            "prefixes" => [
                "xsd" => "http://www.w3.org/2001/XMLSchema#",
                "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
                "rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
                "owl" => "http://www.w3.org/2002/07/owl#",
                "iao" => "http://purl.obolibrary.org/obo/iao.owl#",
                "dc" => "http://purl.org/dc/terms/",

                #utility namespaces
                "obi" => "http://purl.obolibrary.org/obo/obi.owl#",
                "uo" => "http://purl.obolibrary.org/obo/uo.owl#",
                "ncbi" => "https://www.ncbi.nlm.nih.gov/Taxonomy#",
                "mo" => "http://biosemantics.arizona.edu/ontologies/modifierlist#",

                #domain namespaces
                "" => "http://biosemantics.arizona.edu/ontologies/carex#",
                "kb" => "http://biosemantics.arizona.edu/kb/carex#",
                "data" => "http://biosemantics.arizona.edu/kb/data#",
                "app" => "http://shark.sbs.arizona.edu/chrecorder#",
            ],
            "format" => "trig" //Other possible values: n-quads, trig or turtle
        ]);

        $headers = $this->getHeadersByUserId($userID);
        $values = $this->getValuesByCharacter1($userID);
        $colDetails = $this->getColorDetailsById($userID);
        $nonColDetails = $this->getNonColorDetailsById($userID);
        $characterName = $this->getCharacterNameById($userID);


        $index = 1;
        for ($i = count($headers) - 1; $i >= 0; $i--) {
            $header = $headers[$i];
            if ($header['id'] != 1) {
                $writer->addPrefix($specimenName . $index++, "http://biosemantics.arizona.edu/kb/" . str_replace(' ', '/', strtolower($user->taxon)) . "/" . str_replace(' ', '_', $header['header']) . "#");
            }
        }

        $graph = 'data:graph_' . str_replace(' ', '_', strtolower($user->taxon)) . '_' . explode('@', $user->email)[0];

        $index = 0;
        $partNames = [];
        for ($i = count($headers) - 1; $i >= 0; $i--) {
            $types = [];
            $header = $headers[$i];
            if ($header['id'] == 1) {
                continue;
            }

            $index++;

            $sampleName = "kb:" . str_replace(' ', '', ucwords(strtolower($user->taxon))) . str_replace(' ', '_', $header['header']);
            $a = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
            $writer->addTriple($sampleName, "rdf:label", "\"" . $header['header'] . " for " . $user->taxon . "\"", $graph);
            $writer->addTriple($sampleName, "rdf:id", "\"some_Unique_ID_4_This_Sample\"", $graph);
            $writer->addTriple($sampleName, "iao:is_about", "ncbi:txid$txid", $graph);
            $writer->addTriple($sampleName, ":specimen_of", ":" . str_replace(' ', '_', ucwords(strtolower($user->taxon))), $graph);
            $writer->addTriple($sampleName, $a, "obi:specimen", $graph);

            foreach ($values as $character) {
                foreach ($character as $sample) {
                    if ($sample->value != '' && $sample->value != null && $sample->header_id == $header['id']) {
                        $charName = str_replace('-', '_', str_replace(' ', '_', strtolower($characterName[$sample->character_id])));
                        if (strpos($charName, '_between_') !== false) {
                            $charName = str_replace('_between_', '_of_', $charName);
                        };
                        if ($charName == 'shape_of_stem_in_cross_section') {
                            $charName = 'shape_of_stem_in_transverse_section';
                        }
                        if ($this->checkHavingUnit($charName)) {
                            $this->registerPartName($writer, $partNames, $types, $charName, $charName, $sampleName, $graph, $specimenName . $index . ":");
                            if (strpos($charName, 'distance_of_') !== false) {
                                $charName = str_replace('distance_of_', 'distance_between_', $charName);
                            }
                            $sample->value = $sample->value + 0.0;

                            $writer->addTriple($specimenName . "$index:$charName", ":has_value", "\"$sample->value\"^^xsd:float", $graph);
                            if (!startsWith($charName, 'number_of_')
                                && !startsWith($charName, 'count_of_')
                                && !startsWith($charName, 'ratio_of_')
                            ) {
                                $writer->addTriple($specimenName . "$index:$charName", ":has_unit", "uo:$sample->unit", $graph);
                            }
                        } else {
                            if ($colDetails[$sample->id] || $nonColDetails[$sample->id]) {
                                $cols = startsWith($charName, 'color_of_') ? $colDetails[$sample->id] : $nonColDetails[$sample->id];
                                for ($j = 0; $j < count($cols); $j++) {
                                    if ($cols[$j]->negation) {
                                        $partName = explode('_of_', $charName)[1];
                                        $partName = $this->partNameConverter($partName);
                                        if (startsWith($charName, 'color_of_')) {
                                            $this->writeColorDetail($writer, $specimenName . "$index:" . str_replace(' ', '_', $this->getColorDetailText($cols[$j])), $cols[$j], $graph);
                                        } else {
                                            $this->writeNonColorDetail($writer, $specimenName . "$index:" . str_replace(' ', '_', $this->getNonColorDetailText($cols[$j])), $cols[$j], $graph);
                                        }
                                        $writer->addTriple("[]", "rdf:type", "owl:NegativePropertyAssertion", $graph);
                                        $writer->addTriple("", "owl:sourceIndividual", $specimenName . "$index:" . $partName, $graph);
                                        $writer->addTriple("", "owl:sourceIndividual", $specimenName . "$index:" . $partName, $graph);
                                        $writer->addTriple("", "owl:assertionProperty", ":has_quality", $graph);

                                        if (startsWith($charName, 'color_of_')) {
                                            $writer->addTriple("", "owl:targetIndividual", "$specimenName" . "$index:" . str_replace(' ', '_', $this->getColorDetailText($cols[$j])), $graph);
                                        } else {
                                            $writer->addTriple("", "owl:targetIndividual", "$specimenName" . "$index:" . str_replace(' ', '_', $this->getNonColorDetailText($cols[$j])), $graph);
                                        }
                                        continue;
                                    }
                                    $qualityName = $charName . "_" . ($j + 1);
                                    if (count($cols) == 1) {
                                        $qualityName = $charName;
                                    }
                                    $this->registerPartName($writer, $partNames, $types, $charName, $qualityName, $sampleName, $graph, $specimenName . "$index:");
                                    if (startsWith($charName, 'color_of_')) {
                                        $this->writeColorDetail($writer, $specimenName . "$index:" . $qualityName, $cols[$j], $graph);
                                    } else {
                                        $this->writeNonColorDetail($writer, $specimenName . "$index:" . $qualityName, $cols[$j], $graph);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $txt = $graph . " dc:creator app:" . explode('@', $user->email)[0] . ";\n";
        $txt .= "\t:export_date \"" . date("Y-m-d") . "T" . date("h:i:s") . "\"^^xsd:dateTime.";

        return [
            'description' => $writer->end() . $txt,
            'taxons' => $taxons,
        ];
    }

    public function exportDescriptionTrig(Request $request)
    {

        $user = User::where('id', '=', Auth::id())->first();

        $fileName = $user->taxon;
        $file = fopen($fileName . ".trig", "w") or die("Unable to open file!");

        $taxons = [];
        $result = $this->getTrigDescription(Auth::id(), $taxons)['description'];

        fwrite($file, $result);

        // fwrite($file, $writer->end());
        // fwrite($file, $txt);


        fclose($file);

        return array(
            'is_success' => 1,
            'doc_url' => config('constants.root_path') . $fileName . '.trig',
        );
    }

    public function getTaxonID($userId)
    {

        $fetchdata = [];
        $user = User::where('id', '=', $userId)->first();

        $client = new Client(['base_uri' => 'https://eutils.ncbi.nlm.nih.gov']);

        // $response = $client->request('GET', 'http://github.com');
        // echo $response->getStatusCode();
        // Initiate each request but do not block
        $promises = [
            'taxonId' => $client->getAsync('/entrez/eutils/esearch.fcgi', ['query' => ['db' => 'taxonomy', 'term' => $user->taxon]]),
        ];

        // Wait for the requests to complete; throws a ConnectException
        // if any of the requests fail
        $responses = Promise\unwrap($promises);

        // Wait for the requests to complete, even if some of them fail
        $responses = Promise\settle($promises)->wait();

        // You can access each response using the key of the promise
        $xml = $responses['taxonId']['value']->getBody()->getContents();

        $xmlObject = simplexml_load_string($xml);

        //Encode the SimpleXMLElement object into a JSON string.
        $jsonString = json_encode($xmlObject);

        //Convert it back into an associative array for
        //the purposes of testing.
        $jsonArray = json_decode($jsonString, true);

        if (array_key_exists('IdList', $jsonArray)) {
            if (count($jsonArray['IdList'])) {
                return $jsonArray['IdList']['Id'];
            } else {
                return "unknown";
            }
        } else {
            return "unknown";
        }

    }

    public function test()
    {

        $users = User::where('password', '!=', '')->get();
        {
            $client = new Client(['base_uri' => 'https://shark.sbs.arizona.edu:8443/blazegraph/namespace/kb/']);

            $promises = [
                'taxonId' => $client->postAsync('sparql', ['form_params' => ['update' => 'DELETE { ?book ?p ?v } WHERE { ?book ?p ?v .}']]),
            ];

            $responses = Promise\unwrap($promises);

            // Wait for the requests to complete, even if some of them fail
            $responses = Promise\settle($promises)->wait();
        }
        $taxons = [];
        foreach ($users as $user) {
            $client = new Client(['base_uri' => 'https://shark.sbs.arizona.edu:8443/blazegraph/namespace/kb/']);

            $result = $this->getTrigDescription($user->id, $taxons);
            echo $user->id . '<br/>';
            echo $result['description'] . '<br/>';
            echo implode("<br/>", $result['taxons']) . '<br/>';
            $taxons = $result['taxons'];
            $query = 'insert data{' . $result['description'] . '}';
            $promises = [
                'taxonId' => $client->postAsync('sparql', ['form_params' => ['update' => $query]]),
            ];

            $responses = Promise\unwrap($promises);

            // Wait for the requests to complete, even if some of them fail
            $responses = Promise\settle($promises)->wait();
        }


        $fileName = 'http://shark.sbs.arizona.edu'.config('constants.root_path').'ontology/carex.ttl';

        $read = file_get_contents($fileName);

        {
            $client = new Client(['base_uri' => 'https://shark.sbs.arizona.edu:8443/blazegraph/namespace/kb/']);

            $result = $this->getTrigDescription($user->id, $taxons);
            $taxons = $result['taxons'];
            $query = 'insert data{' . $read . '}';
            $promises = [
                'taxonId' => $client->postAsync('sparql', ['form_params' => ['update' => $query]]),
            ];

            $responses = Promise\unwrap($promises);

            // Wait for the requests to complete, even if some of them fail
            $responses = Promise\settle($promises)->wait();
        }

    }

    public function setIRI(Request $request)
    {
        $standardCharacter = StandardCharacter::find($request->input('id'));
        if ($standardCharacter) {
            $standardCharacter->IRI = $request->input('IRI');
            $standardCharacter->save();
        }
    }

    public function setCharacterIRI(Request $request)
    {
        Character::where('name', '=', $request->input('name'))->update(['IRI' => $request->input('IRI')]);
    }

    public function getCharacterNames(Request $request)
    {
        return Character::select('name')->distinct('name')->get();
    }

    public function setNonColorValueIRI(Request $request)
    {
        $id = $request->input('id');
        $main_value_IRI = $request->input('main_value_IRI');
        DB::table('non_color_details')->where('id', '=', $id)->update(['main_value_IRI' => $main_value_IRI]);
    }

    public function setColorBrightnessIRI(Request $request)
    {
        $id = $request->input('id');
        $main_value_IRI = $request->input('IRI');
        DB::table('color_details')->where('id', '=', $id)->update(['brightness_IRI' => $main_value_IRI]);
    }

    public function setColorReflectanceIRI(Request $request)
    {
        $id = $request->input('id');
        $main_value_IRI = $request->input('IRI');
        DB::table('color_details')->where('id', '=', $id)->update(['reflectance_IRI' => $main_value_IRI]);
    }

    public function setColorSaturationIRI(Request $request)
    {
        $id = $request->input('id');
        $main_value_IRI = $request->input('IRI');
        ColorDetails::where('id', '=', $id)->update(['saturation_IRI' => $main_value_IRI]);
    }

    public function setColorColoredIRI(Request $request)
    {
        $id = $request->input('id');
        $main_value_IRI = $request->input('IRI');
        ColorDetails::where('id', '=', $id)->update(['colored_IRI' => $main_value_IRI]);
    }

    public function setColorMultiColoredIRI(Request $request)
    {
        $id = $request->input('id');
        $main_value_IRI = $request->input('IRI');
        ColorDetails::where('id', '=', $id)->update(['multi_colored_IRI' => $main_value_IRI]);
    }

    public function resolveCharacter(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        $deprecatedIRI = $request->input('deprecatedIRI');
        $replacementTerm = $request->input('replacementTerm');
        $replacementIRI = $request->input('replacementIRI');
        $characterResult = Character::where([['IRI', '=', $deprecatedIRI], ['owner_name', '=', $username]])->get();
        foreach ($characterResult as $eachCharacter) {
            $eachCharacter->IRI = $replacementIRI;
            $eachCharacter->name = $replacementTerm;
            $eachCharacter->save();
            $valuesResult = Value::where('character_id', '=', $eachCharacter->id)->get();
            foreach ($valuesResult as $indValue) {
                if ($indValue->header_id == 1) {
                    $indValue->value = $replacementTerm;
                    $indValue->save();
                }
            }
        }
        $standardCharacterResult = StandardCharacter::where('IRI', '=', $deprecatedIRI)->get();
        foreach ($standardCharacterResult as $eachCharacter) {
            $eachCharacter->IRI = $replacementIRI;
            $eachCharacter->name = $replacementTerm;
            $eachCharacter->save();
            $valuesResult = Value::where('character_id', '=', $eachCharacter->id)->get();
            foreach ($valuesResult as $indValue) {
                if ($indValue->header_id == 1) {
                    $indValue->value = $replacementTerm;
                    $indValue->save();
                }
            }
        }

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnDefaultCharacters = $this->getDefaultCharacters();
        $returnTaxon = $user->taxon;

        $returnAllDetailValues = $this->getAllDetails();
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

    public function resolveNonColorValue(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $deprecatedIRI = $request->input('deprecatedIRI');
        $replacementTerm = $request->input('replacementTerm');
        $replacementIRI = $request->input('replacementIRI');
        $nonColorsResult = NonColorDetails::where('main_value_IRI', '=', $deprecatedIRI)->get();
        foreach ($nonColorsResult as $eachValue) {
            $value = Value::where('id', '=', $eachValue->value_id)->first();
            if ($value) {
                $header = Header::where('id', '=', $value->header_id)->first();
                if ($header->user_id == Auth::id()) {
                    $eachValue->main_value_IRI = $replacementIRI;
                    $eachValue->main_value = $replacementTerm;
                    $eachValue->save();
                }
            }
        }

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllDetails();

        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function resolveColorBrightness(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $deprecatedIRI = $request->input('deprecatedIRI');
        $replacementTerm = $request->input('replacementTerm');
        $replacementIRI = $request->input('replacementIRI');
        $colorsResult = ColorDetails::where('brightness_IRI', '=', $deprecatedIRI)->get();
        foreach ($colorsResult as $eachValue) {
            $value = Value::where('id', '=', $eachValue->value_id)->first();
            if ($value) {
                $header = Header::where('id', '=', $value->header_id)->first();
                if ($header->user_id == Auth::id()) {
                    $eachValue->brightness_IRI = $replacementIRI;
                    $eachValue->brightness = $replacementTerm;
                    $eachValue->save();
                }
            }
        }

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllDetails();

        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function resolveColorReflectance(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $deprecatedIRI = $request->input('deprecatedIRI');
        $replacementTerm = $request->input('replacementTerm');
        $replacementIRI = $request->input('replacementIRI');
        $colorsResult = ColorDetails::where('reflectance_IRI', '=', $deprecatedIRI)->get();
        foreach ($colorsResult as $eachValue) {
            $value = Value::where('id', '=', $eachValue->value_id)->first();
            if ($value) {
                $header = Header::where('id', '=', $value->header_id)->first();
                if ($header->user_id == Auth::id()) {
                    $eachValue->reflectance_IRI = $replacementIRI;
                    $eachValue->reflectance = $replacementTerm;
                    $eachValue->save();
                }
            }
        }

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllDetails();

        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function resolveColorSaturation(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $deprecatedIRI = $request->input('deprecatedIRI');
        $replacementTerm = $request->input('replacementTerm');
        $replacementIRI = $request->input('replacementIRI');
        $colorsResult = ColorDetails::where('saturation_IRI', '=', $deprecatedIRI)->get();
        foreach ($colorsResult as $eachValue) {
            $value = Value::where('id', '=', $eachValue->value_id)->first();
            if ($value) {
                $header = Header::where('id', '=', $value->header_id)->first();
                if ($header->user_id == Auth::id()) {
                    $eachValue->saturation_IRI = $replacementIRI;
                    $eachValue->saturation = $replacementTerm;
                    $eachValue->save();
                }
            }
        }

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllDetails();

        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function resolveColorColored(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $deprecatedIRI = $request->input('deprecatedIRI');
        $replacementTerm = $request->input('replacementTerm');
        $replacementIRI = $request->input('replacementIRI');
        $colorsResult = ColorDetails::where('colored_IRI', '=', $deprecatedIRI)->get();
        foreach ($colorsResult as $eachValue) {
            $value = Value::where('id', '=', $eachValue->value_id)->first();
            if ($value) {
                $header = Header::where('id', '=', $value->header_id)->first();
                if ($header->user_id == Auth::id()) {
                    $eachValue->colored_IRI = $replacementIRI;
                    $eachValue->colored = $replacementTerm;
                    $eachValue->save();
                }
            }
        }

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllDetails();

        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function resolveColorMultiColored(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $deprecatedIRI = $request->input('deprecatedIRI');
        $replacementTerm = $request->input('replacementTerm');
        $replacementIRI = $request->input('replacementIRI');
        $colorsResult = ColorDetails::where('multi_colored_IRI', '=', $deprecatedIRI)->get();
        foreach ($colorsResult as $eachValue) {
            $value = Value::where('id', '=', $eachValue->value_id)->first();
            if ($value) {
                $header = Header::where('id', '=', $value->header_id)->first();
                if ($header->user_id == Auth::id()) {
                    $eachValue->multi_colored_IRI = $replacementIRI;
                    $eachValue->multi_colored = $replacementTerm;
                    $eachValue->save();
                }
            }
        }

        $returnValues = $this->getValuesByCharacter();
        $returnAllDetailValues = $this->getAllDetails();

        $data = [
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
        ];

        return $data;
    }

    public function getMatrixNames()
    {
        $names = Matrix::where('user_id', '=', Auth::id())->select('matrix_name')->get();
        return $names;
    }

    public function nameMatrixFunc($matrixName)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        $fakeUser = User::create([
            'email' => $username . '_ver_' . $matrixName . '@email.com',
            'password' => '',
            'remember_token' => '',
            'taxon' => $user['taxon']
        ]);

        $matrix = Matrix::create([
            'user_id' => $user['id'],
            'matrix_name' => $matrixName,
            'named_user_id' => $fakeUser['id']
        ]);

        $usertags = UserTag::where('user_id', '=', Auth::id())->select('user_id', 'tag_name')->get();
        $newUserTags = [];
        foreach ($usertags as $usertag) {
            array_push($newUserTags, [
                'user_id' => $fakeUser['id'],
                'tag_name' => $usertag['tag_name'],
                'created_at' => date("Y-m-d") . " " . date("H:i:s"),
                'updated_at' => date("Y-m-d") . " " . date("H:i:s")
            ]);
        }
        UserTag::insert($newUserTags);

        $characters = Character::where('owner_name', '=', $username)->get();
        $newCharacters = [];
        foreach ($characters as $eachCharacter) {
            array_push($newCharacters, [
                'name' => $eachCharacter['name'],
                'IRI' => $eachCharacter['IRI'],
                'parent_term' => $eachCharacter['parent_term'],
                'method_from' => $eachCharacter['method_from'],
                'method_to' => $eachCharacter['method_to'],
                'method_include' => $eachCharacter['method_include'],
                'method_exclude' => $eachCharacter['method_exclude'],
                'method_where' => $eachCharacter['method_where'],
                'method_as' => $eachCharacter['method_as'],
                'creator' => $eachCharacter['creator'],
                'unit' => $eachCharacter['unit'],
                'standard' => $eachCharacter['standard'],
                'username' => $eachCharacter['username'],
                'owner_name' => $username . '_ver_' . $matrixName,
                'standard_tag' => $eachCharacter['standard_tag'],
                'summary' => $eachCharacter['summary'],
                'usage_count' => $eachCharacter['usage_count'],
                'order' => $eachCharacter['order'],
                'show_flag' => $eachCharacter['show_flag'],
                'created_at' => date("Y-m-d") . " " . date("H:i:s"),
                'updated_at' => date("Y-m-d") . " " . date("H:i:s")
            ]);
        }
        Character::insert($newCharacters);

        $newCharacters = Character::where('owner_name', '=', $username . '_ver_' . $matrixName)->get();
        $characterIds = [];
        foreach ($characters as $eachCharacter) {
            foreach ($newCharacters as $eachNewCharacter) {
                if ($eachCharacter['name'] == $eachNewCharacter['name'] &&
                    $eachCharacter['IRI'] == $eachNewCharacter['IRI'] &&
                    $eachCharacter['method_from'] == $eachNewCharacter['method_from'] &&
                    $eachCharacter['method_to'] == $eachNewCharacter['method_to'] &&
                    $eachCharacter['method_include'] == $eachNewCharacter['method_include'] &&
                    $eachCharacter['method_exclude'] == $eachNewCharacter['method_exclude'] &&
                    $eachCharacter['method_where'] == $eachNewCharacter['method_where'] &&
                    $eachCharacter['method_as'] == $eachNewCharacter['method_as'] &&
                    $eachCharacter['creator'] == $eachNewCharacter['creator'] &&
                    $eachCharacter['unit'] == $eachNewCharacter['unit'] &&
                    $eachCharacter['standard'] == $eachNewCharacter['standard'] &&
                    $eachCharacter['username'] == $eachNewCharacter['username'] &&
                    $eachCharacter['standard_tag'] == $eachNewCharacter['standard_tag'] &&
                    $eachCharacter['summary'] == $eachNewCharacter['summary'] &&
//                    $eachCharacter['usage_count'] == $eachNewCharacter['usage_count'] &&
                    $eachCharacter['order'] == $eachNewCharacter['order'] &&
                    $eachCharacter['show_flag'] == $eachNewCharacter['show_flag']) {
                    $characterIds[$eachCharacter['id']] = $eachNewCharacter['id'];
                    break;
                }
            }
        }

        $headers = Header::where('user_id', '=', Auth::id())->get();
        $newHeaders = [];
        foreach ($headers as $eachHeader) {
            array_push($newHeaders, [
                'header' => $eachHeader['header'],
                'user_id' => $fakeUser['id'],
                'created_at' => date("Y-m-d") . " " . date("H:i:s"),
                'updated_at' => date("Y-m-d") . " " . date("H:i:s")
            ]);
        }
        Header::insert($newHeaders);

        $newHeaders = Header::where('user_id', '=', $fakeUser['id'])->get();
        $headerIds = [];
        foreach ($headers as $eachHeader) {
            foreach ($newHeaders as $eachNewHeader) {
                if ($eachHeader['header'] == $eachNewHeader['header']) {
                    $headerIds[$eachHeader['id']] = $eachNewHeader['id'];
                    break;
                }
            }
        }

        $values = Value::join('characters', 'characters.id', '=', 'values.character_id')
            ->where('characters.owner_name', '=', $username)
            ->select('values.id as id',
                'values.character_id as character_id',
                'values.header_id as header_id',
                'values.value as value')
            ->get();
        $newValues = [];
        foreach ($values as $eachValue) {
            array_push($newValues, [
                'character_id' => $characterIds[$eachValue['character_id']],
                'header_id' => $eachValue['header_id'] == 1 ? 1 : $headerIds[$eachValue['header_id']],
                'value' => $eachValue['value'],
                'created_at' => date("Y-m-d") . " " . date("H:i:s"),
                'updated_at' => date("Y-m-d") . " " . date("H:i:s")
            ]);
        }
        Value::insert($newValues);

        $newValues = Value::join('characters', 'characters.id', '=', 'values.character_id')
            ->where('characters.owner_name', '=', $username . '_ver_' . $matrixName)
            ->select('values.id as id',
                'values.character_id as character_id',
                'values.header_id as header_id',
                'values.value as value')
            ->get();
        $valueIds = [];
        foreach ($values as $eachValue) {
            foreach ($newValues as $eachNewValue) {
                if ($eachNewValue['character_id'] == $characterIds[$eachValue['character_id']] &&
                    ($eachValue['header_id'] == 1 || $eachNewValue['header_id'] == $headerIds[$eachValue['header_id']])) {
                    $valueIds[$eachValue['id']] = $eachNewValue['id'];
                    break;
                }
            }
        }

        $valueIdsResults = Value::join('headers', 'headers.id', '=', 'values.header_id')->where('headers.user_id', '=', $user['id'])->select('values.id')->get();
        $ids = [];
        foreach ($valueIdsResults as $value) {
            array_push($ids, $value->id);
        }
        $colorDetails = ColorDetails::whereIn('value_id', $ids)->get();
        $newColorDetails = [];
        foreach ($colorDetails as $eachColorDetail) {
            array_push($newColorDetails, [
                'value_id' => $valueIds[$eachColorDetail['value_id']],
                'negation' => $eachColorDetail['negation'],
                'pre_constraint' => $eachColorDetail['pre_constraint'],
                'certainty_constraint' => $eachColorDetail['certainty_constraint'],
                'degree_constraint' => $eachColorDetail['degree_constraint'],
                'brightness' => $eachColorDetail['brightness'],
                'brightness_IRI' => $eachColorDetail['brightness_IRI'],
                'reflectance' => $eachColorDetail['reflectance'],
                'reflectance_IRI' => $eachColorDetail['reflectance_IRI'],
                'saturation' => $eachColorDetail['saturation'],
                'saturation_IRI' => $eachColorDetail['saturation_IRI'],
                'colored' => $eachColorDetail['colored'],
                'colored_IRI' => $eachColorDetail['colored_IRI'],
                'multi_colored' => $eachColorDetail['multi_colored'],
                'multi_colored_IRI' => $eachColorDetail['multi_colored_IRI'],
                'post_constraint' => $eachColorDetail['post_constraint'],
                'created_at' => date("Y-m-d") . " " . date("H:i:s"),
                'updated_at' => date("Y-m-d") . " " . date("H:i:s")
            ]);
        }
        ColorDetails::insert($newColorDetails);

        $nonColorDetails = NonColorDetails::whereIn('value_id', $ids)->get();
        $newNonColorDetails = [];
        foreach ($nonColorDetails as $eachNonColorDetail) {
            array_push($newNonColorDetails, [
                'value_id' => $valueIds[$eachNonColorDetail['value_id']],
                'negation' => $eachNonColorDetail['negation'],
                'pre_constraint' => $eachNonColorDetail['pre_constraint'],
                'certainty_constraint' => $eachNonColorDetail['certainty_constraint'],
                'degree_constraint' => $eachNonColorDetail['degree_constraint'],
                'main_value' => $eachNonColorDetail['main_value'],
                'main_value_IRI' => $eachNonColorDetail['main_value_IRI'],
                'post_constraint' => $eachNonColorDetail['post_constraint'],
                'created_at' => date("Y-m-d") . " " . date("H:i:s"),
                'updated_at' => date("Y-m-d") . " " . date("H:i:s")
            ]);
        }
        NonColorDetails::insert($newNonColorDetails);

        $matrixNames = Matrix::where('user_id', '=', Auth::id())->select('matrix_name')->get();

        $data = [
            'fakeUser' => $fakeUser,
            'matrix' => $matrix,
            'usertags' => $newUserTags,
            'headers' => $newHeaders,
            'characterids' => $characterIds,
            'headerIds' => $headerIds,
            'values' => $values,
            'valueIds' => $valueIds,
            'colorDetails' => $colorDetails,
            'nonColorDetails' => $nonColorDetails,
            'matrixNames' => $matrixNames
        ];

        return $data;
    }

    public function nameMatrix(Request $request)
    {
        $matrixName = $request->input('matrixname');
        $data = $this->nameMatrixFunc($matrixName);
        User::where('id', '=', Auth::id())->update(['last_matrix' => $matrixName]);
        return $data;
    }

    public function importMatrix(Request $request)
    {
        $matrixName = $request->input('matrixname');
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        $versionUser = User::where('email', '=', $username . '_ver_' . $matrixName . '@email.com')->first();

        Matrix::where('user_id', '=', $user['id'])->update(['user_id' => $versionUser['id']]);
        User::where('id', '=', $user['id'])->delete();
        $oldCharacterList = Character::where('owner_name', '=', $username)->get();
        foreach ($oldCharacterList as $eachOldCharacter) {
            $valueList = Value::where('character_id', '=', $eachOldCharacter->id)->get();
            foreach ($valueList as $eachValue) {
                NonColorDetails::where('value_id', '=', $eachValue->id)->delete();
            }
            Value::where('character_id', '=', $eachOldCharacter->id)->delete();
        }
        Character::where('owner_name', '=', $username)->delete();

        Matrix::where([['user_id', '=', $versionUser['id']], ['matrix_name', '=', $matrixName]])->delete();

        User::where('id', '=', $versionUser['id'])->update([
            'id' => $user['id'],
            'email' => $user['email'],
            'password' => $user['password'],
            'remember_token' => $user['remember_token'],
            'last_matrix'  => $matrixName
//            'taxon' => $user['taxon']
        ]);
        Character::where('owner_name', '=', $username . '_ver_' . $matrixName)->update(['owner_name' => $username]);

        $characterList = Character::where('owner_name', '=', $username)->get()->toArray();

        foreach ($characterList as $eachCharacter) {
            $characterValues = Value::where('character_id', '=', $eachCharacter['id'])->get();
            $tempCount = 0;
            foreach ($characterValues as $eachValue) {
                if (ColorDetails::where('value_id', '=', $eachValue->id)->count() > 0 || NonColorDetails::where('value_id', '=', $eachValue->id)->count() > 0) {
                    $tempCount++;
                }
            }
            if ($tempCount > 0) {
                Character::where('id', '=', $eachCharacter['id'])->update(['usage_count' => 1]);
            }
        }



        $this->nameMatrixFunc($matrixName);

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnUserTags = UserTag::where('user_id', '=', Auth::id())->get();
        $returnAllDetailValues = $this->getAllDetails();

        $data = [
            'headers' => $returnHeaders,
            'characters' => $returnCharacters,
            'values' => $returnValues,
            'allColorValues' => $returnAllDetailValues['colorValues'],
            'allNonColorValues' => $returnAllDetailValues['nonColorValues'],
            'tags' => $returnUserTags,
            'taxon' => $versionUser['taxon']
        ];

        return $data;
    }

    public function overwriteMatrix(Request $request)
    {
        $matrixName = $request->input('matrixname');
        User::where('id', '=', Auth::id())->update(['last_matrix' => $matrixName]);
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        $versionUser = User::where('email', '=', $username . '_ver_' . $matrixName . '@email.com')->first();

        Matrix::where([['user_id', '=', $user['id']], ['matrix_name', '=', $matrixName]])->delete();
        User::where('id', '=', $versionUser['id'])->delete();
        Character::where('owner_name', '=', $username . '_ver_' . $matrixName)->delete();

        $this->nameMatrixFunc($matrixName);

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnUserTags = UserTag::where('user_id', '=', Auth::id())->get();
        $returnAllDetailValues = $this->getAllDetails();

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

    public function getTaxons(Request $request)
    {
        $taxons = User::distinct()->get(['taxon']);

        $data = [
            'taxons' => $taxons
        ];

        return $data;
    }

    public function resetSystem(Request $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];
        Character::whereraw('1')->delete();
        ColorDetails::whereraw('1')->delete();
        NonColorDetails::whereraw('1')->delete();
        Header::where('id', '!=', 1)->delete();
        Matrix::whereraw('1')->delete();
        UserTag::whereraw('1')->delete();
        User::where('password', '')->delete();
        User::where('id', '=', Auth::id())->update(['last_matrix' => '']);
        DefaultCharacter::whereRaw('1')->delete();
    }

    public function updateStandardCharacter()
    {
        $client = new Client(['base_uri' => 'http://shark.sbs.arizona.edu:8080']);

        // $response = $client->request('GET', 'http://github.com');
        // echo $response->getStatusCode();
        // Initiate each request but do not block
        $promises = [
            'characters' => $client->getAsync('/carex/getStandardCollection'),
        ];

        // Wait for the requests to complete; throws a ConnectException
        // if any of the requests fail
        $responses = Promise\unwrap($promises);

        // Wait for the requests to complete, even if some of them fail
        $responses = Promise\settle($promises)->wait();

        $bodyResponse = $responses['characters']['value']->getBody();

        $bodyJson = json_decode($bodyResponse, true);
        if ($bodyJson) {
            StandardCharacter::whereraw('1')->delete();
            foreach ($bodyJson['entries'] as $character) {
                $updatedCharacter = [
                    'name' => ucfirst($character['term']),
                    'parent_term' => $character['parentTerm'],
                    'IRI' => null,
                    'method_from' => null,
                    'method_to' => null,
                    'method_include' => null,
                    'method_exclude' => null,
                    'method_where' => null,
                    'unit' => null,
                    'creator' => null,
                    'standard' => 1,
                    'standard_tag' => null,
                    'created_at' => null,
                    'updated_at' => null,
                    'username' => null,
                    'usage_count' => 0,
                    'show_flag' => 1,
                ];
                foreach ($character['resultAnnotations'] as $property) {
                    switch ($property['property']) {
                        case 'http://www.geneontology.org/formats/oboInOwl#id':
                            $updatedCharacter['IRI'] = $property['value'];
                            break;
                        case 'http://www.geneontology.org/formats/oboInOwl#created_by':
                            $updatedCharacter['creator'] = $property['value'];
                            $updatedCharacter['username'] = $property['value'];
                            break;
                        case 'http://www.geneontology.org/formats/oboInOwl#creation_date':
                            $createdDate = date_create_from_format('m-d-Y', $property['value']);
                            if ($createdDate) {
                                $updatedCharacter['created_at'] = date_format($createdDate, 'Y-m-d H:i:s');
                            }
                            break;
                        case 'http://biosemantics.arizona.edu/ontologies/carex#updated_date':
                            $updatedDate = date_create_from_format('m-d-Y', $property['value']);
                            if ($updatedDate) {
                                $updatedCharacter['updated_at'] = date_format($updatedDate, 'Y-m-d H:i:s');
                            }
                            break;
                        case 'http://biosemantics.arizona.edu/ontologies/carex#quality_of_organ_group':
                            $strIndex = strrpos($property['value'], "#");
                            $updatedCharacter['standard_tag'] = ucfirst(str_replace("_", " ", substr($property['value'], $strIndex + 1)));
                            break;
                        case 'http://biosemantics.arizona.edu/ontologies/carex#measured_from':
                            $strIndex = strrpos($property['value'], "#");
                            if ($updatedCharacter['method_from']) {
                                $updatedCharacter['method_from'] = $updatedCharacter['method_from'] . ' and ' . str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            } else {
                                $updatedCharacter['method_from'] = str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            }
                            break;
                        case 'http://biosemantics.arizona.edu/ontologies/carex#measured_to':
                            $strIndex = strrpos($property['value'], "#");
                            if ($updatedCharacter['method_to']) {
                                $updatedCharacter['method_to'] = $updatedCharacter['method_to'] . ' and ' . str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            } else {
                                $updatedCharacter['method_to'] = str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            }
                            break;
                        case 'http://biosemantics.arizona.edu/ontologies/carex#measured_include':
                            $strIndex = strrpos($property['value'], "#");
                            if ($updatedCharacter['method_include']) {
                                $updatedCharacter['method_include'] = $updatedCharacter['method_include'] . ' and ' . str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            } else {
                                $updatedCharacter['method_include'] = str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            }
                            break;
                        case 'http://biosemantics.arizona.edu/ontologies/carex#measured_exclude':
                            $strIndex = strrpos($property['value'], "#");
                            if ($updatedCharacter['method_exclude']) {
                                $updatedCharacter['method_exclude'] = $updatedCharacter['method_exclude'] . ' and ' . str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            } else {
                                $updatedCharacter['method_exclude'] = str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            }
                            break;
                        case 'http://biosemantics.arizona.edu/ontologies/carex#measured_at':
                            $strIndex = strrpos($property['value'], "#");
                            if ($updatedCharacter['method_where']) {
                                $updatedCharacter['method_where'] = $updatedCharacter['method_where'] . ' and ' . str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            } else {
                                $updatedCharacter['method_where'] = str_replace("_", " ", substr($property['value'], $strIndex + 1));
                            }
                            break;
                        default:
                            break;
                    }
                }
                if ($updatedCharacter['standard_tag'] == null) {
                    $updatedCharacter['standard_tag'] = 'Habit';
                }
                StandardCharacter::insert($updatedCharacter);
            }
        }

    }

    public function getStandardTags()
    {
        return StandardCharacter::select('standard_tag')->distinct('standard_tag')->get();
    }

    public function newMatrix()
    {
        $user = User::where('id', '=', Auth::id())->first();
        $user->update(['last_matrix' => '']);
        $username = explode('@', $user['email'])[0];

        Character::where('owner_name', '=', $username)->delete();
        Header::where('user_id', '=', $user['id'])->delete();
        UserTag::where('user_id', '=', $user['id'])->delete();

        $returnHeaders = $this->getHeaders();
        $returnValues = $this->getValuesByCharacter();
        $returnCharacters = $this->getArrayCharacters();
        $returnUserTags = UserTag::where('user_id', '=', Auth::id())->get();
        $returnAllDetailValues = $this->getAllDetails();

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

    public function getUsers()
    {
        $users = User::where('password', '!=', '')->get();
        $usernames = [];
        foreach ($users as $user) {
            $username = explode('@', $user['email'])[0];
            array_push($usernames, $username);
        }

        return $usernames;
    }

    // fetch the data for character values who has already defined
    public function searchCharacter() {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_SSL_VERIFYPEER => false
        ); 
        $ch = curl_init('http://shark.sbs.arizona.edu:8080/carex/getValidSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term=anatomical_structure');
        curl_setopt_array($ch, $options);
        $content  = curl_exec($ch);
        curl_close($ch);
        $resArr = array();
        $resArr = json_decode($content,true);
        $characters = array();
        if(!empty($resArr)) {
            if(isset($resArr['children']) && !empty($resArr['children'])){
                foreach ($resArr['children'] as $ckey => $cvalue) {
                    if(!empty($cvalue['text'])) {
                        $characters[] = $cvalue['text'];
                    }  
                    if(isset($cvalue['children']) && count($cvalue['children']) > 0) {
                        foreach ($cvalue['children'] as $ch_key => $child) {
                            if(!empty($cvalue['text'])) {
                                $characters[] = $child['text'];    
                            }

                            if(isset($child['children']) && count($child['children']) > 0) {
                              foreach($child['children'] as $ch_child) {
                                if(!empty($cvalue['text'])) {
                                    $characters[] = $ch_child['text'];    
                                }
                              }
                            } 
                        }
                    }
                }
            }       
        }
        sort($characters);
        return $characters;
    }

    public function emptyCells(Request $request) {
        $input = $request->all();
        if(!empty($input)) {
           foreach ($input as $key => $value) {
                if($key != '_token'){
                    Value::where('character_id',$key)
                    ->where('header_id','!=',1)
                    ->where(function($q) { 
                        $q->whereNull('value')->orWhere('value','=','');
                    })->update(['value'=>$value]);
                } 
           }
        }
        return redirect('/');
    }

    public function characterValues() {
      return CharacterValue::pluck('value');
    }
}
