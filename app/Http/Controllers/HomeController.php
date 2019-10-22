<?php

namespace App\Http\Controllers;

use App\character;
use App\ColorDetails;
use App\NonColorDetails;
use App\Value;
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
        $beforeOneWeek = date('Y-m-d h:m:s', strtotime('-7 days'));

        $allValues = Value::all();
//        dd(strtotime($date) > strtotime($allValues[830]['updated_at']));
        $resultList = [];
        foreach ($allValues as $eachValue) {
            if ($eachValue->header_id != 1
                && $eachValue->value != null
                && strtotime($eachValue->updated_at) > strtotime($beforeOneWeek)) {
                $character = Character::where('id', '=', $eachValue->character_id)->first();
                if (array_key_exists($character->owner_name, $resultList)) {
                    $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                } else {
                    $resultList[$character->owner_name] = 1;
                }
            } else {
                $valueDetail = ColorDetails::where('value_id', '=', $eachValue->id)->whereDate('updated_at', '>=', $beforeOneWeek)->first();
                if ($valueDetail) {
                    $character = Character::where('id', '=', $eachValue->character_id)->first();
                    if (array_key_exists($character->owner_name, $resultList)) {
                        $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                    } else {
                        $resultList[$character->owner_name] = 1;
                    }
                } else {
                    $valueDetail = NonColorDetails::where('value_id', '=', $eachValue->id)->whereDate('updated_at', '>=', $beforeOneWeek)->first();
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

        if ($resultList == []) {
            $resultList['allFlag'] = true;

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
                        $valueDetail = NonColorDetails::where('value_id', '=', $eachValue->id)->whereDate('updated_at', '>=', $beforeOneWeek)->first();
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

        } else {
            $resultList['allFlag'] = false;
        }

        return view('leaderboard',  ['list' => $resultList]);
    }

}
