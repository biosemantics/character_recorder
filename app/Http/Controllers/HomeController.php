<?php

namespace App\Http\Controllers;

use App\Character;
use App\StandardCharacter;
use App\ColorDetails;
use App\NonColorDetails;
use App\Value;
use App\User;

use DB;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function leaderBoard()
    {
        $allValues = Value::all();
        $resultList = [];

        foreach ($allValues as $eachValue) {
            if ($eachValue->header_id != 1
                && $eachValue->value != null) {
                $character = Character::where('id', '=', $eachValue->character_id)->first();
                if (array_key_exists($character->owner_name, $resultList)) {
                    $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                } else {
                    $resultList[$character->owner_name] = 1;
                }
            } else {
                $valueDetail = ColorDetails::where('value_id', '=', $eachValue->id)->first();
                if ($valueDetail) {
                    $character = Character::where('id', '=', $eachValue->character_id)->first();
                    if (array_key_exists($character->owner_name, $resultList)) {
                        $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                    } else {
                        $resultList[$character->owner_name] = 1;
                    }
                } else {
                    $valueDetail = NonColorDetails::where('value_id', '=', $eachValue->id)->first();
                    if ($valueDetail) {
                        $character = Character::where('id', '=', $eachValue->character_id)->first();
                        if (array_key_exists($character->owner_name, $resultList)) {
                            $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                        } else {
                            $resultList[$character->owner_name] = 1;
                        }
                    }
                }
            }
        }

        return view('leaderboard', ['list' => $resultList]);
    }

    public function exploreCharacter()
    {
        // $standardCharacters = StandardCharacter::all();
        // $standardUsages = Character::join('standard_characters','standard_characters.name','=','characters.name')->where('standard_characters.username','=','characters.username')->select('standard_characters.id as id', DB::raw('sum(characters.usage_count) as usage_count'))->groupBy('standard_characters.id')->get();

        // foreach ($standardUsages as $su) {
        //     foreach($standardCharacters as $sc){
        //         if ($sc->id == $su->id){
        //             $sc->usage_count = $su->usage_count;
        //             break;
        //         }
        //     }
        // }
        
        // foreach($standardCharacters as $sc){
        //     if (!$sc->usage_count){
        //         $sc->usage_count = 0;
        //     }
        // }

        // $standardCharacters = $standardCharacters->toArray();

        // $userCharacters = Character::where('standard', '=', 0)
        //     ->whereRaw('username LIKE CONCAT("%", owner_name)')
        //     ->get();
        // $userUsages = DB::table('characters as A')->join('characters as B', 'A.name', '=', 'B.name')->where('A.standard','=',0)->whereRaw('A.username like concat("%", A.owner_name)')->where('A.username','=','B.username')->select('A.id as id',DB::raw('sum(B.usage_count) as usage_count'))->groupBy('A.id')->get();
        // foreach ($userUsages as $uu) {
        //     foreach ($userCharacters as $uc){
        //         if ($uc->id == $uu->id){
        //             $uc->usage_count = $uu->usage_count;
        //             break;
        //         }
        //     }
        // }
        // $userCharacters = $userCharacters->toArray();

        // $defaultCharacters = array_merge($standardCharacters, $userCharacters);

        // return view('explorecharacter', ['list' => $defaultCharacters]);
        return view('explorecharacter');
    }

}
