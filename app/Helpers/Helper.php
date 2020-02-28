<?php

use App\Character;
use App\ColorDetails;
use App\NonColorDetails;
use App\Value;

if (!function_exists('getRandomPhrase')) {
    /**
     * Returns a randomize string from array
     *
     * @return string a string in human readable format
     *
     * */
    function getRandomPhrase()
    {
        $arr = ['enabling reuse of trait data',
            'making your data long lived',
            'building the trait data infrastructure for the future'];
        return $arr[rand(0, 2)];
    }
}

if (!function_exists('getTopUser')) {
    /**
     * Returns a top user for leader board
     *
     * @return string a string in human readable format
     *
     * */
    function getTopUser()
    {
        // $beforeOneWeek = date('Y-m-d h:m:s', strtotime('-7 days'));

        $allValues = Value::all();
        $resultList = [];
        // foreach ($allValues as $eachValue) {
        //     if ($eachValue->header_id != 1
        //         && $eachValue->value != null
        //         && strtotime($eachValue->updated_at) > strtotime($beforeOneWeek)) {
        //         $character = Character::where('id', '=', $eachValue->character_id)->first();
        //         if (array_key_exists($character->owner_name, $resultList)) {
        //             $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
        //         } else {
        //             $resultList[$character->owner_name] = 1;
        //         }
        //     } else {
        //         $valueDetail = ColorDetails::where('value_id', '=', $eachValue->id)->whereDate('updated_at', '>=', $beforeOneWeek)->first();
        //         if ($valueDetail) {
        //             $character = Character::where('id', '=', $eachValue->character_id)->first();
        //             if (array_key_exists($character->owner_name, $resultList)) {
        //                 $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
        //             } else {
        //                 $resultList[$character->owner_name] = 1;
        //             }
        //         } else {
        //             $valueDetail = NonColorDetails::where('value_id', '=', $eachValue->id)->whereDate('updated_at', '>=', $beforeOneWeek)->first();
        //             if ($valueDetail) {
        //                 $character = Character::where('id', '=', $eachValue->character_id)->first();
        //                 if (array_key_exists($character->owner_name, $resultList)) {
        //                     $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
        //                 } else {
        //                     $resultList[$character->owner_name] = 1;
        //                 }
        //             }

        //         }
        //     }
        // }

        $lastWeekFlag = false;

        if ($resultList == []) {
            $lastWeekFlag = true;

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
        }

        $maxValue = 0;
        $maxKey = '';
        foreach ($resultList as $key => $eachValue) {
            if ($resultList[$key] > $maxValue) {
                $maxKey = $key;
                $maxValue = $resultList[$key];
            }
        }

        $result = $maxKey . ' recorded ' . $maxValue . ' characters';
        if ($lastWeekFlag == false) {
            $result = $result . ' last week';
        }

        return $result;
    }
}