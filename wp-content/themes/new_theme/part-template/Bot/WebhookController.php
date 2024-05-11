<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Models\LigaHistory;
use App\Models\LigaMission;
use App\Models\Parameter;
use App\Models\ProfileModel as Profile;
use App\Models\TwitterPointModel as TwitterPoint;
use App\Models\UsersModel as Users;
use App\Models\UsersPointModel as UsersPoint;
use App\Models\WA\WABotKeyword;
use App\Models\WA\WAChatHistory;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookController extends Controller {
    public function __Construct() {
        $this->client = new \GuzzleHttp\Client();
        $this->url = 'https://service-chat.qontak.com/api/open/v1';
        $this->channel_integration_id = Parameter::where( 'key', 'whatsapp_integration_id' )->first()->value;
        $this->authentication_whatsapp_token = Parameter::where( 'key', 'whatsapp_token' )->first()->value;
        $this->wa_business_number = Parameter::where( 'key', 'wa_business_number' )->first()->value;
    }

    public function receive( Request $request ) {
        /* test no agus dan kang opik */
        if (
            $request->room[ 'account_uniq_id' ] == '6281285544200' ||
            $request->room[ 'account_uniq_id' ] == '6281910469248' ||
            $request->room[ 'account_uniq_id' ] == '62816280576' ||
            $request->room[ 'account_uniq_id' ] == '6281283212168' ||
            $request->room[ 'account_uniq_id' ] == '62855999111' ||
            $request->room[ 'account_uniq_id' ] == '6285608392936' ||
            $request->room[ 'account_uniq_id' ] == '6281282251136' ||
            $request->room[ 'account_uniq_id' ] == '6285721397529' ||
            $request->room[ 'account_uniq_id' ] == '6285692151699' ||
            $request->room[ 'account_uniq_id' ] == '628158086146' ||
            $request->room[ 'account_uniq_id' ] == '6282230007021' ||
            $request->room[ 'account_uniq_id' ] == '6282217185656' ||
            $request->room[ 'account_uniq_id' ] == '628112249383' ||
            $request->room[ 'account_uniq_id' ] == '628977878980' ||
            $request->room[ 'account_uniq_id' ] == '6282114303110' ||
            $request->room[ 'account_uniq_id' ] == '6285693387995'
        ) {

            $message = $request->text;

            /* update room id in profile */
            $profile = Profile::where( 'phone_number', $request->room[ 'account_uniq_id' ] )->first();
            if ( $profile != null ) {
                $profile->room_id = $request->room[ 'id' ];
                $profile->save();
            }

            /* cek chat keyword bot */
            $parameter = Parameter::where( 'key', 'keyword_bot' )->first();

            /* pengecekan message dengan keyword access bot sesuai parameter atau tidak */
            if ( strtolower( $parameter->value ) == strtolower( $message ) || strtolower( $message ) == 'keluar') {
                /* jika sesuai akses bot dan simpan chat bot*/
                if ( $profile != null ) {
                    $cek_role = Users::with( 'role' )->where( 'id', $profile->id_users )->first();
                    if ( $cek_role->role->code == 'SADM' ) {
                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_super' )->first();
                    } else if ( $cek_role->role->code == 'ADM' ) {
                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_adm' )->first();
                    } else if ( $cek_role->role->code == 'APNTP' ) {
                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_accessor' )->first();
                    } else if ( $cek_role->role->code == 'MMBR' ) {
                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_member' )->first();
                    } else {
                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_general' )->first();
                    }
                } else {
                    $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_general' )->first();
                }

                $params = array(
                    'wa_bot_keyword' => $wa_bot_keyword->keyword,
                    'room_id' => $request->room[ 'id' ],
                    'type' => $request->type,
                    'text' => str_replace( '<br>', '\n', $wa_bot_keyword->question ),
                    'is_outside_bot' => 0,
                );

                $this->createHistoryChat( $request, $params );
            } else {
                /* jika message tidak sesuai keyword access bot */
                $message_response = preg_replace( '/[^a-zA-Z0-9]/', '', $message );
                $message_chat = strtolower( $message_response );

                /* pengecekan chat bot terakhir dari customer ke agent atau agent ke customer */
                $wa_chat_history_bot = WAChatHistory::whereIn( 'from', [ $this->wa_business_number, $request->room[ 'account_uniq_id' ] ] )
                ->whereIn( 'to', [ $request->room[ 'account_uniq_id' ], $this->wa_business_number ] )
                ->where( 'wa_bot_keyword', '!=', '-' )
                ->orderBy( 'created_at', 'DESC' )
                ->first();

                /* pengambilan next question berdasarkan wabot keyword terbaru dari chat  */
                if ( $wa_chat_history_bot != null ) {
                    /* lingkup chat bot */
                    $wa_bot_keyword = WABotKeyword::where( 'keyword', $wa_chat_history_bot->wa_bot_keyword )->first();

                    /* ini untuk handle setelah bot di close dan ada chat masuk biasa */
                    if ( $wa_bot_keyword->answer != '-' ) {

                        /* get answer dan next question */
                        $answer = explode( ',', str_replace( ' ', '', strtolower( $wa_bot_keyword->answer ) ) );
                        $next_question = explode( ',', str_replace( ' ', '', strtolower( $wa_bot_keyword->next_question ) ) );

                        /* replace simbol, spasi dan get data wabot keyword */
                        $answer_index = array_search( $message_chat, preg_replace( '/[^a-zA-Z0-9]/', '', $answer ) );

                        /* jika array index tidak ketemu bernilai false maka jangan masuk ke send reply chat, ini yang menyebabkan chat reply berulang */
                        /* sekaligus untuk handle chat diluar jawaban bot */
                        if ( $answer_index !== false ) {
                            $question_index = str_replace( ' ', '', $next_question[ $answer_index ] );

                            $wa_bot_keyword_by_index = WABotKeyword::where( 'id', $question_index )->first();
                            
                            /* handle misi liga */
                            if ( $wa_bot_keyword_by_index->keyword == 'gaspol_liga' ) {
                                $liga_history = LigaHistory::where( 'date_start', '<=', date( 'Y-m-d' ) )
                                ->where( 'date_end', '>=', date( 'Y-m-d' ) )
                                ->where( 'deleted_at', null )
                                ->orderBy( 'date_start', 'ASC' )
                                ->get();

                                $list_liga = [];
                                foreach ( $liga_history as $key => $item ) {
                                    array_push( $list_liga, ( $key + 1 ) . '. ' . $item->title );
                                }

                                if ( count( $list_liga ) < 1 ) {
                                    $wa_bot_liga_not_found = WABotKeyword::where( 'keyword', 'gaspol_liga_not_found' )->first();
                                    $question = str_replace( '<br>', '\n', $wa_bot_liga_not_found->question );
                                } else {
                                    $question_before = str_replace( '<br>', '\n', $wa_bot_keyword_by_index->question );
                                    $question = str_replace( '{{1}}', implode( '\n', $list_liga ), $question_before );
                                }
                            } else if ( $wa_bot_keyword_by_index->keyword == 'gaspol_leaderboard' ) {
                                /* handle leaeerboard liga */
                                $liga_history = LigaHistory::where( 'date_start', '<=', date( 'Y-m-d' ) )
                                ->where( 'date_end', '>=', date( 'Y-m-d' ) )
                                ->where( 'deleted_at', null )
                                ->orderBy( 'date_start', 'ASC' )
                                ->get();

                                $list_liga = [];
                                foreach ( $liga_history as $key => $item ) {
                                    array_push( $list_liga, ( $key + 1 ) . '. ' . $item->title );
                                }

                                if ( count( $list_liga ) < 1 ) {
                                    $wa_bot_liga_not_found = WABotKeyword::where( 'keyword', 'gaspol_liga_not_found' )->first();
                                    $question = str_replace( '<br>', '\n', $wa_bot_liga_not_found->question );
                                } else {
                                    $question_before = str_replace( '<br>', '\n', $wa_bot_keyword_by_index->question );
                                    $question = str_replace( '{{1}}', implode( '\n', $list_liga ), $question_before );
                                }
                            } else if ( $wa_bot_keyword_by_index->keyword == 'gaspol_point' ) {
                                /* handle poin saya */

                                $get_all_point = UsersPoint::select( DB::raw( 'COALESCE(SUM(point), 0) as all_point' ) )
                                ->where( 'id_users', $profile->id_users )
                                ->first();

                                $all_point_kreator = UsersPoint::select( DB::raw( 'COALESCE(SUM(point), 0) as all_point' ) )
                                ->where( 'id_users', $profile->id_users )
                                ->where( 'id_liga_history', null )
                                ->first();

                                $get_recent_point = UsersPoint::select( 'point', 'title', 'id_liga_history' )
                                ->where( 'id_users', $profile->id_users )
                                ->join( 'liga_history', 'liga_history.id', '=', 'users_point.id_liga_history' )
                                ->orderBy( 'users_point.created_at', 'DESC' )
                                ->first();

                                if ( $get_recent_point != null ) {
                                    $get_twitter_point = TwitterPoint::select( DB::raw( 'COALESCE(SUM(total_point), 0) as twitter_point' ) )
                                    ->where( 'id_users', $profile->id_users )
                                    ->where( 'id_liga_history', $get_recent_point->id_liga_history )
                                    ->first();

                                    $value_twitter_point = $get_twitter_point->twitter_point;

                                } else {
                                    $value_twitter_point = 0;
                                }

                                $all_point = $get_all_point ? $get_all_point->all_point : 0;
                                $recent_point = $get_recent_point ? $get_recent_point->point : 0;
                                $recent_name_liga = $get_recent_point ? $get_recent_point->title : 'Kreator';
                                $twitter_point = $value_twitter_point;
                                $facebook_point = 0;
                                $instagram_point = 0;
                                $youtube_point = 0;
                                $tiktok_point = 0;
                                $kreator_point = $all_point_kreator ? $all_point_kreator->all_point : 0;

                                $question_before = str_replace( '<br>', '\n', $wa_bot_keyword_by_index->question );
                                $question = str_replace( '{{1}}', $recent_name_liga, $question_before );
                                $question = str_replace( '{{2}}', $recent_point, $question );
                                $question = str_replace( '{{3}}', $twitter_point, $question );
                                $question = str_replace( '{{4}}', $facebook_point, $question );
                                $question = str_replace( '{{5}}', $youtube_point, $question );
                                $question = str_replace( '{{6}}', $tiktok_point, $question );
                                $question = str_replace( '{{7}}', $instagram_point, $question );
                                $question = str_replace( '{{8}}', $all_point, $question );
                                $question = str_replace( '{{9}}', $kreator_point, $question );
                            } else if ( $wa_bot_keyword_by_index->keyword == 'gaspol_profile' ) {
                                /* handle update profil */
                                $profile = Profile::where( 'phone_number', $request->room[ 'account_uniq_id' ] )->first();

                                $name = $profile->name;
                                $twitter = $profile->twitter_username;
                                $facebook = $profile->facebook_username;
                                $tiktok = $profile->tiktok_username;
                                $instagram = $profile->instagram_username;
                                $youtube = $profile->youtube_username;

                                $question_before = str_replace( '<br>', '\n', $wa_bot_keyword_by_index->question );
                                $question = str_replace( '{{1}}', $name, $question_before );
                                $question = str_replace( '{{2}}', $twitter, $question );
                                $question = str_replace( '{{3}}', $facebook, $question );
                                $question = str_replace( '{{4}}', $youtube, $question );
                                $question = str_replace( '{{5}}', $tiktok, $question );
                                $question = str_replace( '{{6}}', $instagram, $question );
                            } else if ( $wa_bot_keyword_by_index->keyword == 'gaspol_register' ){
                                /* handle register liga */
                                $liga_history = LigaHistory::where('date_start', '<=', date('Y-m-d'))
                                ->where('date_end', '>=', date('Y-m-d'))
                                ->where('deleted_at', null)
                                ->orderBy('date_start', 'ASC')
                                ->get();
    
                                $list_liga = [];
                                foreach ($liga_history as $key => $item) {
                                    array_push($list_liga, ($key+1).'. '. $item->title);
                                }

                                $question = str_replace("{{1}}", implode( '\n', $list_liga ), $wa_bot_keyword_by_index->question);
                            } else {
                                /* handle informasi tanpa interaksi database */
                                $question = str_replace( '<br>', '\n', $wa_bot_keyword_by_index->question );
                            }

                            /* lanjutan akses bot dan simpan chat bot*/
                            $params = array(
                                'wa_bot_keyword' => $wa_bot_keyword_by_index->keyword,
                                'room_id' => $request->room[ 'id' ],
                                'type' => $request->type,
                                'text' => str_replace( '<br>', '\n', $question ),
                                'is_outside_bot' => 0,
                            );

                            $this->createHistoryChat( $request, $params );
                        } else {
                            $is_profile = false;
                            if (strpos($wa_bot_keyword->keyword, 'profile') !== false) {
                                $is_profile = true;
                            }

                            /* handle untuk interaksi database */
                            if ( $wa_bot_keyword->keyword == 'gaspol_liga' || $wa_bot_keyword->keyword == 'gaspol_misi' ) {

                                $liga_history = LigaHistory::where( 'date_start', '<=', date( 'Y-m-d' ) )
                                ->where( 'date_end', '>=', date( 'Y-m-d' ) )
                                ->where( 'deleted_at', null )
                                ->orderBy( 'date_start', 'ASC' )
                                ->get();

                                $list_liga = [];
                                foreach ( $liga_history as $key => $item ) {
                                    $list_liga[ $key ] = $item->id;
                                }

                                /* handle reply bot, agent or customer */
                                $is_reply = intval( $message_chat );
                                if ( $is_reply > 0 ) {
                                    $next_question = $list_liga[ $is_reply - 1 ];

                                    $type_misi = [ 'Harian', 'Mingguan', 'Bulanan', 'Triwulan', 'Semester', 'Tahunan' ];

                                    $mission = [];
                                    foreach ( $type_misi as $type ) {
                                        $mission_data = LigaMission::where( 'type', $type )->where( 'id_liga_history', $next_question )->get();

                                        if ( count( $mission_data ) > 0 ) {
                                            array_push( $mission, $mission_data );
                                        }
                                    }

                                    /* handle jika misi kosong */
                                    if ( count( $mission ) > 0 ) {
                                        $list_mission = [];
                                        foreach ( $mission as $key => $item ) {
                                            if ( count( $item ) > 0 ) {
                                                array_push( $list_mission, ( $key + 1 ) . '. ' . $item[ 0 ]->misi );
                                            }
                                        }

                                        $mission_post = implode( '\n', $list_mission );

                                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_misi' )->first();
                                        $question_mission = str_replace( '{{1}}', $mission_post, $wa_bot_keyword->question );
                                        $question = str_replace( '<br>', '\n', $question_mission );

                                        $params = array(
                                            'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                            'room_id' => $request->room[ 'id' ],
                                            'type' => $request->type,
                                            'text' => $question,
                                            'is_outside_bot' => 0,
                                        );
                                    } else {
                                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_misi_not_found' )->first();
                                        $question = str_replace( '<br>', '\n', $wa_bot_keyword->question );

                                        $params = array(
                                            'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                            'room_id' => $request->room[ 'id' ],
                                            'type' => $request->type,
                                            'text' => $question,
                                            'is_outside_bot' => 0,
                                        );
                                    }

                                    $this->createHistoryChat( $request, $params );
                                } else {
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'text' => str_replace( '\n', '<br>', $request->text ),
                                        'is_outside_bot' => 0,
                                    );
                                    $this->createHistoryBotFromAgentOrBot( $request, $params );
                                }
                            } else if ( $wa_bot_keyword->keyword == 'gaspol_leaderboard' || $wa_bot_keyword->keyword == 'gaspol_rank' ) {
                                $liga_history = LigaHistory::where( 'date_start', '<=', date( 'Y-m-d' ) )
                                ->where( 'date_end', '>=', date( 'Y-m-d' ) )
                                ->where( 'deleted_at', null )
                                ->orderBy( 'date_start', 'ASC' )
                                ->get();

                                $list_liga = [];
                                foreach ( $liga_history as $key => $item ) {
                                    $list_liga[ $key ] = $item->id;
                                }

                                /* handle reply bot, agent or customer */
                                $is_reply = intval( $message_chat );
                                if ( $is_reply > 0 ) {
                                    $next_question = $list_liga[ ( $message_chat - 1 ) ];

                                    /* cek users termasuk rank apa */
                                    $rank_profile = Profile::select( 'users.rank', 'users.id' )
                                    ->join( 'users', 'users.id', '=', 'profile.id_users' )
                                    ->where( 'phone_number', $request->room[ 'account_uniq_id' ] )
                                    ->first();

                                    /* get users poin berdasarkan liga dan berdasarkan rank */
                                    $member = Users::select( 'users.id', 'profile.name' )
                                    ->join( 'profile', 'profile.id_users', '=', 'users.id' )
                                    ->where( 'rank', $rank_profile->rank )
                                    ->where( 'id_users', '!=', $rank_profile->id )
                                    ->limit( 50 )
                                    ->get();

                                    $list_member_point = [];
                                    foreach ( $member as $key => $user ) {
                                        $users_point = UsersPoint::select( DB::raw( 'SUM(point) as point' ) )
                                        ->where( 'id_users', $user->id )
                                        ->where( 'id_liga_history', $next_question )
                                        ->first();

                                        array_push( $list_member_point, $user->name . ': ' . ( $users_point->point ?? 0 ) );
                                    }

                                    $list_member = array_reverse( $list_member_point );

                                    $question_list = [];
                                    foreach ( $list_member as $key => $item ) {
                                        array_push( $question_list, ( $key + 1 ) . '. ' . $item );
                                    }

                                    $question_post = implode( '\n', $question_list );

                                    $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_rank' )->first();
                                    $question_rank = str_replace( '{{1}}', $question_post, $wa_bot_keyword->question );
                                    $question = str_replace( '<br>', '\n', $question_rank );

                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'room_id' => $request->room[ 'id' ],
                                        'type' => $request->type,
                                        'text' => $question,
                                        'is_outside_bot' => 0,
                                    );
                                    $this->createHistoryChat( $request, $params );
                                } else {
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'text' => str_replace( '\n', '<br>', $request->text ),
                                        'is_outside_bot' => 0,
                                    );
                                    $this->createHistoryBotFromAgentOrBot( $request, $params );
                                }
                            } else if ( $wa_bot_keyword->keyword == 'gaspol_close' ) {
                                /* handle chat bot setelah close and auto reply bot greeting next */
                                if ( $profile != null ) {
                                    $cek_role = Users::with( 'role' )->where( 'id', $profile->id_users )->first();
                                    if ( $cek_role->role->code == 'SADM' ) {
                                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_super' )->first();
                                    } else if ( $cek_role->role->code == 'ADM' ) {
                                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_adm' )->first();
                                    } else if ( $cek_role->role->code == 'APNTP' ) {
                                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_accessor' )->first();
                                    } else if ( $cek_role->role->code == 'MMBR' ) {
                                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_member' )->first();
                                    } else {
                                        $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_general' )->first();
                                    }
                                } else {
                                    $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_greeting_general' )->first();
                                }

                                $question = str_replace( '<br>', '\n', $wa_bot_keyword->question );

                                $params = array(
                                    'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                    'room_id' => $request->room[ 'id' ],
                                    'type' => $request->type,
                                    'text' => $question,
                                    'is_outside_bot' => 0,
                                );

                                $this->createHistoryChat( $request, $params );
                            } else if ( $wa_bot_keyword->keyword == 'gaspol_register') {
                                /* handle register reply bot */
                                $liga_history = LigaHistory::where('date_start', '<=', date('Y-m-d'))
                                ->where('date_end', '>=', date('Y-m-d'))
                                ->where('deleted_at', null)
                                ->orderBy('date_start', 'ASC')
                                ->get();
    
                                $list_liga = [];
                                foreach ($liga_history as $key => $item) {
                                    array_push($list_liga, $item->id);
                                }

                                /* handle reply bot, agent or customer */
                                $is_reply = intval($message_chat);
                                if($is_reply > 0) {
                                    $next_question = $list_liga[$is_reply-1];

                                    $wa_bot_keyword = WABotKeyword::where('keyword', 'gaspol_register_link')->first();
                                    $question_link = str_replace("{{1}}", $next_question, $wa_bot_keyword->question);
                                    $question = str_replace("<br>", "\n", $question_link);
                                
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'room_id' => $request->room['id'],
                                        'type' => $request->type,
                                        'text' => $question,
                                        'is_outside_bot' => 0
                                    );
                                    $this->createHistoryChat($request, $params);
                                }else{
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'text' => str_replace("\n", "<br>", $request->text),
                                        'is_outside_bot' => 0
                                    );
                                    $this->createHistoryBotFromAgentOrBot($request, $params);
                                }


                            } else if ( $wa_bot_keyword->keyword == 'gaspol_region' ) {
                                /* handle gaspol region */
                                $get_list_region = Parameter::where( 'key', 'rank_users' )->first();
                                $list_region = explode( ',', str_replace( ' ', '', $get_list_region->value ) );

                                /* handle reply bot, agent or customer */
                                $is_reply = intval( $message_chat );
                                if ( $is_reply > 0 ) {
                                    $next_question = $list_region[ $is_reply - 1 ];

                                    $users_point = [];
                                    if ( $next_question == 'DPC' ) {

                                        $users_point = UsersPoint::select( 'id_districts.name', DB::raw( 'SUM(users_point.point) as point' ) )
                                        ->join( 'users', 'users.id', '=', 'users_point.id_users' )
                                        ->join( 'id_districts', 'id_districts.id', '=', 'users.kecamatan' )
                                        ->groupBy( 'name' )
                                        ->get();

                                    } else if ( $next_question == 'DPD' ) {

                                        $users_point = UsersPoint::select( 'id_cities.name', DB::raw( 'SUM(users_point.point) as point' ) )
                                        ->join( 'users', 'users.id', '=', 'users_point.id_users' )
                                        ->join( 'id_cities', 'id_cities.id', '=', 'users.kota_kab' )
                                        ->groupBy( 'name' )
                                        ->get();

                                    } else if ( $next_question == 'DPW' ) {

                                        $users_point = UsersPoint::select( 'id_provinces.name', DB::raw( 'SUM(users_point.point) as point' ) )
                                        ->join( 'users', 'users.id', '=', 'users_point.id_users' )
                                        ->select( 'id_provinces.name', DB::raw( 'SUM(users_point.point) as point' ) )
                                        ->join( 'id_provinces', 'id_provinces.id', '=', 'users.provinsi' )
                                        ->groupBy( 'name' )
                                        ->get();

                                    }

                                    $list_point_region = [];
                                    foreach ( $users_point as $key => $item ) {
                                        array_push( $list_point_region, ( $key + 1 ) . '. ' . $item->name . ' : ' . $item->point );
                                    }

                                    $list_region_bot = implode( '\n', array_reverse( $list_point_region ) );

                                    $wa_bot_keyword = WABotKeyword::where( 'keyword', 'gaspol_region_answer' )->first();
                                    $question = str_replace( '<br>', '\n', $wa_bot_keyword->question );
                                    $question = str_replace( '{{1}}', $next_question, $question );
                                    $question = str_replace( '{{2}}', $list_region_bot, $question );

                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'room_id' => $request->room[ 'id' ],
                                        'type' => $request->type,
                                        'text' => $question,
                                        'is_outside_bot' => 0,
                                    );
                                    $this->createHistoryChat( $request, $params );
                                } else {
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'text' => str_replace( '\n', '<br>', $request->text ),
                                        'is_outside_bot' => 0,
                                    );
                                    $this->createHistoryBotFromAgentOrBot( $request, $params );
                                }
                            } else if($is_profile == true) {
                                /* handel update profile */
                                if($request->participant_type == 'customer') {
                                    $profile = Profile::where( 'phone_number', $request->room[ 'account_uniq_id' ] )->first();

                                    if($wa_bot_keyword->keyword == 'gaspol_profile_name') {

                                        $profile->name = $request->text;

                                    }else if($wa_bot_keyword->keyword == 'gaspol_profile_twitter') {

                                        $profile->twitter_username = $request->text;
                                        $profile->twitter_id = '';

                                    }else if($wa_bot_keyword->keyword == 'gaspol_profile_facebook') {

                                        $profile->facebook_username = $request->text;
                                        $profile->facebook_id = '';

                                    }else if($wa_bot_keyword->keyword == 'gaspol_profile_youtube') {

                                        $profile->youtube_username = $request->text;
                                        $profile->youtube_id = '';

                                    }else if($wa_bot_keyword->keyword == 'gaspol_profile_tiktok') {

                                        $profile->tiktok_username = $request->text;
                                        $profile->tiktok_id = '';

                                    }else if($wa_bot_keyword->keyword == 'gaspol_profile_instagram') {

                                        $profile->instagram_username = $request->text;
                                        $profile->instagram_id = '';

                                    }

                                    $profile->save();

                                    $profile = Profile::where( 'phone_number', $request->room[ 'account_uniq_id' ] )->first();

                                    $name = $profile->name;
                                    $twitter = $profile->twitter_username;
                                    $facebook = $profile->facebook_username;
                                    $tiktok = $profile->tiktok_username;
                                    $instagram = $profile->instagram_username;
                                    $youtube = $profile->youtube_username;

                                    $wa_bot_keyword = WABotKeyword::where('keyword', 'gaspol_profile')->first();
                                    $question = str_replace("<br>", "\n", $wa_bot_keyword->question);
                                    $question = str_replace( '{{1}}', $name, $question );
                                    $question = str_replace( '{{2}}', $twitter, $question );
                                    $question = str_replace( '{{3}}', $facebook, $question );
                                    $question = str_replace( '{{4}}', $youtube, $question );
                                    $question = str_replace( '{{5}}', $tiktok, $question );
                                    $question = str_replace( '{{6}}', $instagram, $question );
                                
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'room_id' => $request->room['id'],
                                        'type' => $request->type,
                                        'text' => $question,
                                        'is_outside_bot' => 0
                                    );
                                    $this->createHistoryChat($request, $params);
                                }else{
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'text' => str_replace( '\n', '<br>', $request->text ),
                                        'is_outside_bot' => 0,
                                    );
                                    $this->createHistoryBotFromAgentOrBot( $request, $params );
                                }
                                    
                            } else {
                                /* handle chat dari bot / agent diluar maupun didalam chatbot */
                                /* handle chat ketika tidak sesuai jawaban */
                                if ( $request->participant_type == 'customer' ) {
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'text' => str_replace( '\n', '<br>', $request->text ),
                                        'is_outside_bot' => 1,
                                    );

                                    $this->createHistoryBotFromAgentOrBot( $request, $params );

                                    /* ketika chat bot tidak sesuai kirim reply outside chat */
                                    $wa_bot_keyword_outside = WABotKeyword::where( 'keyword', 'gaspol_outside' )->first();
                                    $question = str_replace( '<br>', '\n', $wa_bot_keyword_outside->question );

                                    $params_outside = array(
                                        'wa_bot_keyword' => $wa_bot_keyword_outside->keyword,
                                        'room_id' => $request->room[ 'id' ],
                                        'type' => $request->type,
                                        'text' => $question,
                                        'is_outside_bot' => 0,
                                    );

                                    /* send reply bot wa */
                                    $this->sendReplyBot( $request, $params_outside );
                                } else {
                                    $params = array(
                                        'wa_bot_keyword' => $wa_bot_keyword->keyword,
                                        'text' => str_replace( '\n', '<br>', $request->text ),
                                        'is_outside_bot' => 0,
                                    );

                                    $this->createHistoryBotFromAgentOrBot( $request, $params );
                                }
                            }
                        }
                    } else {
                        /* handle untuk chat close bot */
                        $params = array( 'wa_bot_keyword' => '-', 'is_outside_bot' => 0 );
                        $this->createHistoryChat( $request, $params );
                    }

                } else {
                    /* lingkup chat biasa diluar bot */
                    $params = array( 'wa_bot_keyword' => '-', 'is_outside_bot' => 0 );
                    $this->createHistoryChat( $request, $params );
                }

            }

            return response()->json( [
                'message' => 'Success receive webhook',
            ], 200 );
        } else {
            return response()->json( [
                'message' => 'Failed receive webhook',
            ], 200 );
        }
    }

    private function createHistoryChat( Request $request, $params ) {
        $starter_chat = $this->checkStarterChat( $this->wa_business_number, $request->room[ 'account_uniq_id' ] );

        $is_access = false;
        $wa_chat_check_double = WAChatHistory::where( 'message_id', $request->id )->first();
        // $wa_chat_check_double = null;
        if ( $wa_chat_check_double == null ) {
            $wa_chat_history = new WAChatHistory();
            $wa_chat_history->id = Str::uuid();

            if ( $request->participant_type == 'customer' ) {
                $wa_chat_history->from = $request->room[ 'account_uniq_id' ];
                $wa_chat_history->to = $this->wa_business_number;
                $wa_chat_history->read = 0;
            } else if ( $request->participant_type == 'agent' || $request->participant_type == 'bot' ) {
                $wa_chat_history->from = $this->wa_business_number;
                $wa_chat_history->to = $request->room[ 'account_uniq_id' ];
                $wa_chat_history->read = 1;
            }

            $wa_chat_history->message_id = $request->id;
            $wa_chat_history->message = $request->text;
            $wa_chat_history->wa_bot_keyword = $params[ 'wa_bot_keyword' ];
            $wa_chat_history->type = $request->type;
            $wa_chat_history->starter_chat = $starter_chat;
            $wa_chat_history->is_outside_bot = $params[ 'is_outside_bot' ];
            $wa_chat_history->save();

            $is_access = true;
        }

        if ( $is_access == true && $params[ 'wa_bot_keyword' ] != '-' ) {
            /* send auto reply chat */
            $this->sendReplyBot( $request, $params );
        }

        return $is_access;
    }

    private function sendReplyBot( $request, $params ) {
        $send = $this->client->post( $this->url . '/messages/whatsapp', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => $this->authentication_whatsapp_token,
            ],
            'json'    => $params,
        ] );
        $send = json_decode( $send->getBody()->getContents() );

        return $send;
    }

    private function createHistoryBotFromAgentOrBot( Request $request, $params ) {
        /* sementara by database untuk cek alur */
        $wa_bot_keyword = WabotKeyword::where( 'keyword', $params[ 'wa_bot_keyword' ] )->first();
        $starter_chat = $this->checkStarterChat( $this->wa_business_number, $request->room[ 'account_uniq_id' ] );

        $is_access = false;

        $wa_chat_check_double = WAChatHistory::where( 'message_id', $request->id )->first();
        // $wa_chat_check_double = null;
        if ( $wa_chat_check_double == null ) {
            $wa_chat_history = new WAChatHistory();
            $wa_chat_history->id = Str::uuid();

            if ( $request->participant_type == 'customer' ) {
                $wa_chat_history->from = $request->room[ 'account_uniq_id' ];
                $wa_chat_history->to = $this->wa_business_number;
                $wa_chat_history->read = 0;
            } else if ( $request->participant_type == 'agent' || $request->participant_type == 'bot' ) {
                $wa_chat_history->from = $this->wa_business_number;
                $wa_chat_history->to = $request->room[ 'account_uniq_id' ];
                $wa_chat_history->read = 1;
            }

            $wa_chat_history->message_id = $request->id;
            $wa_chat_history->message = $params[ 'text' ];
            $wa_chat_history->wa_bot_keyword = $params[ 'wa_bot_keyword' ];
            $wa_chat_history->type = $request->type;
            $wa_chat_history->starter_chat = $starter_chat;
            $wa_chat_history->is_outside_bot = $params[ 'is_outside_bot' ];
            $wa_chat_history->save();

            $is_access = true;
        }

        return $is_access;
    }

    private function checkStarterChat( $from, $to ) {
        $tday = date( 'Y-m-d H:i:s' );
        $yday = date( 'Y-m-d H:i:s', strtotime( '-1 day' ) );

        $cek = WAChatHistory::whereIn( 'from', [ $from, $to ] )->whereIn( 'to', [ $from, $to ] )
        ->whereBetween( 'created_at', [ $yday, $tday ] )
        ->where( 'starter_chat', 1 )
        ->first();

        if ( $cek ) {
            return 0;
        } else {
            return 1;
        }

    }
}
