<?php
/**
 * Created by PhpStorm.
 * User: ZEUS
 * Date: 10/23/2019
 * Time: 9:23 PM
 */

namespace App;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MyEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $topUser;
    public $leaderBoard;
    public $detailData;

    public function __construct($detailData = null)
    {
        $beforeOneWeek = date('Y-m-d h:m:s', strtotime('-7 days'));
        // fetch all records corresponding values with characters and  retrieve first row of data from color_details and non_color_details 
        $allValues =    \DB::table('values')
                        ->leftJoin('characters', 'characters.id', '=', 'values.character_id')
                        ->select( 'values.*' ,'characters.id As chId', 'characters.owner_name',
                        \DB::raw('(select value_id from  non_color_details where value_id  =   values.id  order by id asc limit 1) as ncid'),  
                        \DB::raw('(select value_id from  color_details where value_id  =   values.id  order by id asc limit 1) as cid'), 
                        \DB::raw('(select updated_at from  color_details where value_id  =   values.id  order by id asc limit 1) as cDate'),
                        \DB::raw('(select updated_at from  non_color_details where value_id  =   values.id  order by id asc limit 1) as ncDate'),
                        )
                        ->get();

        $resultList = [];
        foreach ($allValues as $eachValue) {
            if ($eachValue->header_id != 1
                && $eachValue->value != null
                && strtotime($eachValue->updated_at) > strtotime($beforeOneWeek)
            ) {
                $currentUser = User::where('email','like', $eachValue->owner_name . '@%')->first();
                if (!empty($currentUser) && $currentUser->password != '') {
                    if (array_key_exists($eachValue->owner_name, $resultList)) {
                        $resultList[$eachValue->owner_name] = $resultList[$eachValue->owner_name] + 1;
                    } else {
                        $resultList[$eachValue->owner_name] = 1;
                    }
                }
            } else {

                if (!empty($eachValue->cid) && (strtotime($eachValue->cDate) >= strtotime($beforeOneWeek))) {
                    $currentUser = User::where('email','like', $eachValue->owner_name . '@%')->first();
                    if (!empty($currentUser) && $currentUser->password != '') {
                        if (array_key_exists($eachValue->owner_name, $resultList)) {
                            $resultList[$eachValue->owner_name] = $resultList[$eachValue->owner_name] + 1;
                        } else {
                            $resultList[$eachValue->owner_name] = 1;
                        }
                    }
                } else {
                    if (!empty($eachValue->ncid) && (strtotime($eachValue->ncDate) >= strtotime($beforeOneWeek))) {
                        $currentUser = User::where('email','like', $eachValue->owner_name . '@%')->first();
                        if (!empty($currentUser) && $currentUser->password != '') {
                            if (array_key_exists($eachValue->owner_name, $resultList)) {
                                $resultList[$eachValue->owner_name] = $resultList[$eachValue->owner_name] + 1;
                            } else {
                                $resultList[$eachValue->owner_name] = 1;
                            }
                        }
                    }

                }
            }
        }

        $lastWeekFlag = false;

        if ($resultList == []) {

            $lastWeekFlag = true;
            $resultList['allFlag'] = true;

            foreach ($allValues as $eachValue) {
                if ($eachValue->header_id != 1
                    && $eachValue->value != null
                ) {
                    $currentUser = User::where('email','like', $eachValue->owner_name . '@%')->first();
                    if (!empty($currentUser) && $currentUser->password != '') {
                        if (array_key_exists($eachValue->owner_name, $resultList)) {
                            $resultList[$eachValue->owner_name] = $resultList[$eachValue->owner_name] + 1;
                        } else {
                            $resultList[$eachValue->owner_name] = 1;
                        }
                    }
                } else { 
                    if (!empty($eachValue->cid)) {
                        $currentUser = User::where('email','like', $eachValue->owner_name . '@%')->first();
                        if (!empty($currentUser) && $currentUser->password != '') {
                            if (array_key_exists($eachValue->owner_name, $resultList)) {
                                $resultList[$eachValue->owner_name] = $resultList[$eachValue->owner_name] + 1;
                            } else {
                                $resultList[$eachValue->owner_name] = 1;
                            }
                        }
                    } else {
                        if (!empty($eachValue->ncid)) {
                            $currentUser = User::where('email','like', $eachValue->owner_name . '@%')->first();
                            if (!empty($currentUser) && $currentUser->password != '') {
                                if (array_key_exists($eachValue->owner_name, $resultList)) {
                                    $resultList[$eachValue->owner_name] = $resultList[$eachValue->owner_name] + 1;
                                } else {
                                    $resultList[$eachValue->owner_name] = 1;
                                }
                            }
                        }
                    }
               }
            }
        } else {
            $resultList['allFlag'] = false;
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

        $this->topUser = $result;

        $this->leaderBoard = $resultList;

        $this->detailData = $detailData;
    }

   /* public function __construct($detailData = null)
    {
        $beforeOneWeek = date('Y-m-d h:m:s', strtotime('-7 days'));

        $allValues = Value::all();
        $resultList = [];
        $allUsers = User::all();
        foreach ($allValues as $eachValue) {
            if ($eachValue->header_id != 1
                && $eachValue->value != null
                && strtotime($eachValue->updated_at) > strtotime($beforeOneWeek)
            ) {
                $character = Character::where('id', '=', $eachValue->character_id)->first();
                $currentUser = null;
                foreach($allUsers as $eachUser) {
                    if (explode('@', $eachUser['email'])[0] == $character->owner_name) {
                        $currentUser = $eachUser;
                        break;
                    }
                }
                if ($currentUser['password'] != '') {
                    if (array_key_exists($character->owner_name, $resultList)) {
                        $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                    } else {
                        $resultList[$character->owner_name] = 1;
                    }
                }
            } else {
                $valueDetail = ColorDetails::where('value_id', '=', $eachValue->id)->whereDate('updated_at', '>=', $beforeOneWeek)->first();
                if ($valueDetail) {
                    $character = Character::where('id', '=', $eachValue->character_id)->first();
                    $currentUser = null;
                    foreach($allUsers as $eachUser) {
                        if (explode('@', $eachUser['email'])[0] == $character->owner_name) {
                            $currentUser = $eachUser;
                            break;
                        }
                    }
                    if ($currentUser['password'] != '') {
                        if (array_key_exists($character->owner_name, $resultList)) {
                            $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                        } else {
                            $resultList[$character->owner_name] = 1;
                        }
                    }
                } else {
                    $valueDetail = NonColorDetails::where('value_id', '=', $eachValue->id)->whereDate('updated_at', '>=', $beforeOneWeek)->first();
                    if ($valueDetail) {
                        $character = Character::where('id', '=', $eachValue->character_id)->first();
                        $currentUser = null;
                        foreach($allUsers as $eachUser) {
                            if (explode('@', $eachUser['email'])[0] == $character->owner_name) {
                                $currentUser = $eachUser;
                                break;
                            }
                        }
                        if ($currentUser['password'] != '') {
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

        $lastWeekFlag = false;

        if ($resultList == []) {
            $lastWeekFlag = true;
            $resultList['allFlag'] = true;

            foreach ($allValues as $eachValue) {
                if ($eachValue->header_id != 1
                    && $eachValue->value != null
                ) {
                    $character = Character::where('id', '=', $eachValue->character_id)->first();
                    $currentUser = null;
                    foreach($allUsers as $eachUser) {
                        if (explode('@', $eachUser['email'])[0] == $character->owner_name) {
                            $currentUser = $eachUser;
                            break;
                        }
                    }
                    if ($currentUser['password'] != '') {
                        if (array_key_exists($character->owner_name, $resultList)) {
                            $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                        } else {
                            $resultList[$character->owner_name] = 1;
                        }
                    }
                } else {
                    $valueDetail = ColorDetails::where('value_id', '=', $eachValue->id)->first();
                    if ($valueDetail) {
                        $character = Character::where('id', '=', $eachValue->character_id)->first();
                        $currentUser = null;
                        foreach($allUsers as $eachUser) {
                            if (explode('@', $eachUser['email'])[0] == $character->owner_name) {
                                $currentUser = $eachUser;
                                break;
                            }
                        }
                        if ($currentUser['password'] != '') {
                            if (array_key_exists($character->owner_name, $resultList)) {
                                $resultList[$character->owner_name] = $resultList[$character->owner_name] + 1;
                            } else {
                                $resultList[$character->owner_name] = 1;
                            }
                        }
                    } else {
                        $valueDetail = NonColorDetails::where('value_id', '=', $eachValue->id)->whereDate('updated_at', '>=', $beforeOneWeek)->first();
                        if ($valueDetail) {
                            $character = Character::where('id', '=', $eachValue->character_id)->first();
                            $currentUser = null;
                            foreach($allUsers as $eachUser) {
                                if (explode('@', $eachUser['email'])[0] == $character->owner_name) {
                                    $currentUser = $eachUser;
                                    break;
                                }
                            }
                            if ($currentUser['password'] != '') {
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
        } else {
            $resultList['allFlag'] = false;
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

        $this->topUser = $result;

        $this->leaderBoard = $resultList;

        $this->detailData = $detailData;
    }*/

    public function broadcastOn()
    {
        return ['my-channel'];
    }

    public function broadcastAs()
    {
        return 'my-event';
    }
}
