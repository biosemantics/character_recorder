<?php

use App\Character;
use App\ColorDetails;
use App\NonColorDetails;
use App\Value;
use App\User;

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
        // fetch all records corresponding values with characters and  retrieve first row of data from color_details and non_color_details  
        $allValues =    DB::table('values')
                        ->leftJoin('characters', 'characters.id', '=', 'values.character_id')
                        ->select( 'values.*' ,'characters.id As chId', 'characters.owner_name',
                        DB::raw('(select value_id from  non_color_details where value_id  =   values.id order by id asc limit 1) as ncid'), 
                        DB::raw('(select value_id from  color_details where value_id  =   values.id order by id asc limit 1) as cid'), 

                        )
                        ->get();
        $resultList = [];
        $lastWeekFlag = false;
        if (count($allValues) > 0) {
            $lastWeekFlag = true;
            foreach ($allValues as $eachValue) {
                if ($eachValue->header_id != 1
                    && $eachValue->value != null) {
                    $currentUserName = $eachValue->owner_name ? explode('_ver_', $eachValue->owner_name)[0]: '';
                    if (array_key_exists($currentUserName, $resultList)) {
                        $resultList[$currentUserName] = $resultList[$currentUserName] + 1;
                    } else {
                        $resultList[$currentUserName] = 1;
                    }
                } else {
                    if (!empty($eachValue->cid)) {
                        $currentUserName = $eachValue->owner_name ? explode('_ver_', $eachValue->owner_name)[0]: '';
                        if (array_key_exists($currentUserName, $resultList)) {
                            $resultList[$currentUserName] = $resultList[$currentUserName] + 1;
                        } else {
                            $resultList[$currentUserName] = 1;
                        }
                    } else {
                        if (!empty($eachValue->ncid)) {
                            $currentUserName = $eachValue->owner_name ? explode('_ver_', $eachValue->owner_name)[0]: '';
                            if (array_key_exists($currentUserName, $resultList)) {
                                $resultList[$currentUserName] = $resultList[$currentUserName] + 1;
                            } else {
                                $resultList[$currentUserName] = 1;
                            }
                        }
                    }
                }
            }
        }
        $maxValue = 0;
        $maxKey = '';
        if(!empty($resultList)) {
            foreach ($resultList as $key => $eachValue) {
                if ($resultList[$key] > $maxValue) {
                    $maxKey = $key;
                    $maxValue = $resultList[$key];
                }
            }
        }
        $result = 'User "' . $maxKey . '" recorded ' . $maxValue . ' characters';
        if ($lastWeekFlag == false) {
            $result = $result . ' last week';
        }
        return $result;
    }
}
