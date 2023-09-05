<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\AccessToken;
use App\Models\WabotKeyword;

class WabotKeywordController extends Controller
{
    public function authentication(Request $request)
    {
        $auth = $request->header('Authorization');
        $token = AccessToken::where('id', $auth)->first();

        $this->is_token = false;

        if($token != null)
            $this->is_token = true;

        return $this->is_token;
    }

    public function retrieve(Request $request)
    {
        if($this->authentication($request) == true) {

            $data = WabotKeyword::where('deleted_at', null)
                    ->where(function($query) use ($request) {
                        $query->where('keyword', 'like', '%'.$request->keyword.'%')
                            ->orWhere('question', 'like', '%'.$request->keyword.'%');
                    })->get();
            
            return response()->json([
                'data' => $data,
                'message' => 'Successfully receive bot keyword'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Invalid auth token',
            ], 500);
        }
    }

    public function retrieve_by_id(Request $request, $id)
    {
        if($this->authentication($request) == true) {

            $data = WabotKeyword::where(function($query) use ($request, $id) {
                        $query->where('id', $id);
                    })->first();
            
            return response()->json([
                'data' => $data,
                'message' => 'Successfully receive bot keyword'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Invalid auth token',
            ], 500);
        }
    }

    public function create(Request $request)
    {
        if($this->authentication($request) == true) {

            $cek = WabotKeyword::where('keyword', $request->keyword)->where('deleted_at', null)->first();

            if($cek == null) {
                $next_question_keyword = str_replace(' ', '', $request->next_question_keyword);
                $next_question = WabotKeyword::whereIn('keyword', explode(',', $next_question_keyword))->get();

                $next_question_id = [];
                foreach($next_question as $item) {
                    array_push($next_question_id, $item->id);
                }

                $data = new WabotKeyword;
                $data->id = Str::uuid();
                $data->keyword = $request->keyword;
                $data->question = $request->question;
                $data->answer = $request->answer;
                $data->next_question = implode(',', $next_question_id);
                $data->next_question_keyword = $request->next_question_keyword;
                $data->save();
            }else{
                return response()->json([
                    'message' => 'Keyword is exist'
                ], 302);
            }

            return response()->json([
                'data' => $data,
                'message' => 'Successfully create bot keyword'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Invalid auth token',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if($this->authentication($request) == true) {
            $next_question_keyword = str_replace(' ', '', $request->next_question_keyword);
            $next_question = WabotKeyword::whereIn('keyword', explode(',', $next_question_keyword))->get();

            $next_question_id = [];
            foreach($next_question as $item) {
                array_push($next_question_id, $item->id);
            }

            $data = WabotKeyword::find($id);
            $data->keyword = $request->keyword;
            $data->question = $request->question;
            $data->answer = $request->answer;
            $data->next_question = implode(',', $next_question_id);
            $data->next_question_keyword = $request->next_question_keyword;
            $data->save();

            return response()->json([
                'data' => $data,
                'message' => 'Successfully update bot keyword'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Invalid auth token',
            ], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        if($this->authentication($request) == true) {

            $data = WabotKeyword::find($id);
            $data->deleted_at = date('Y-m-d H:i:s');
            $data->save();

            return response()->json([
                'message' => 'Successfully delete bot keyword'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Invalid auth token',
            ], 500);
        }
    }
}