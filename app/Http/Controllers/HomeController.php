<?php

namespace App\Http\Controllers;

use App\Character;
use App\StandardCharacter;
use App\ColorDetails;
use App\NonColorDetails;
use App\Value;
use App\User;
use App\Dispute;

use Auth;
use DB;

use Illuminate\Database\Eloquent\Model;
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
        $allValues = Value::join('headers', 'headers.id', '=', 'header_id')->join('users', 'users.id', '=', 'headers.user_id')->where('users.password', '!=', '')->select('values.value as value', 'values.header_id as header_id', 'values.id as id', 'users.email as email')->get();
        $resultList = [];
        $allUsers = User::where('password', '!=', '')->get();

        foreach ($allValues as $eachValue) {
            $name = explode('@', $eachValue->email)[0];
            if ($eachValue->header_id != 1
                && $eachValue->value != null) {
                if (array_key_exists($name, $resultList)) {
                    $resultList[$name] = $resultList[$name] + 1;
                } else {
                    $resultList[$name] = 1;
                }
            } else {
                $valueDetail = ColorDetails::where('value_id', '=', $eachValue->id)->first();
                if ($valueDetail) {
                    if (array_key_exists($name, $resultList)) {
                        $resultList[$name] = $resultList[$name] + 1;
                    } else {
                        $resultList[$name] = 1;
                    }
                }
                else {
                    $valueDetail = NonColorDetails::where('value_id', '=', $eachValue->id)->first();
                    if ($valueDetail) {
                        if (array_key_exists($name, $resultList)) {
                            $resultList[$name] = $resultList[$name] + 1;
                        } else {
                            $resultList[$name] = 1;
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

    public function ontologyUpdate()
    {
        return view('ontologyupdate');
    }

    public function sharedCharacter()
    {
        return view('sharedcharacter');
    }

    public function sendMail(Request $request) {
        $user = User::where('id', '=', Auth::id())->first();
        $username = explode('@', $user['email'])[0];

        $dispute = new Dispute([
            'label' => $request->input('label'),
            'definition' => $request->input('definition'),
            'IRI' => $request->input('IRI'),
            'deprecated_reason' => $request->input('deprecated_reason'),
            'disputed_by' => $username,
            'disputed_reason' => $request->input('message'),
            'new_definition' => $request->input('new_definition'),
            'example_sentence' => $request->input('example_sentence'),
            'taxa' => $request->input('taxa'),
        ]);

        $dispute->save();

        $details = [
            'title' => $request->input('subject'),
            'body' => $request->input('message'),
            'name' => $request->input('name'),
            'email' => $request->input('email')
        ];

        \Mail::to('hongcui@email.arizona.edu')->send(new \App\Mail\MailController($details));
    }

}
