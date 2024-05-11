<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\LigaHistory;
use App\Models\LigaMission;
use App\Models\Parameter;
use App\Models\ProfileModel as Profile;
use App\Models\TwitterPointModel as TwitterPoint;
use App\Models\UsersModel as Users;
use App\Models\UsersPointModel as UsersPoint;
use App\Models\WA\WABotKeyword;
use App\Models\WA\WAChatHistory;
use App\Models\VerifOtp;

class BotWhatsappController extends Controller
{
    public function __Construct()
    {
        $this->client                        = new \GuzzleHttp\Client();
        $this->url                           = 'https://service-chat.qontak.com/api/open/v1';
        $this->channel_integration_id        = Parameter::where('key', 'whatsapp_integration_id')->first()->value;
        $this->authentication_whatsapp_token = Parameter::where('key', 'whatsapp_token')->first()->value;
        $this->wa_business_number            = Parameter::where('key', 'wa_business_number')->first()->value;
    }

    public function receive(Request $request)
    {
        /* handle number access bot */
        // if(
        //     $request->room[ 'account_uniq_id' ] == '6281285544200' ||
        //     $request->room[ 'account_uniq_id' ] == '6281910469248' ||
        //     $request->room[ 'account_uniq_id' ] == '62816280576' ||
        //     $request->room[ 'account_uniq_id' ] == '6281283212168' ||
        //     $request->room[ 'account_uniq_id' ] == '62855999111' ||
        //     $request->room[ 'account_uniq_id' ] == '6285608392936' ||
        //     $request->room[ 'account_uniq_id' ] == '6281282251136' ||
        //     $request->room[ 'account_uniq_id' ] == '6285721397529' ||
        //     $request->room[ 'account_uniq_id' ] == '6285692151699' ||
        //     $request->room[ 'account_uniq_id' ] == '628158086146' ||
        //     $request->room[ 'account_uniq_id' ] == '6282230007021' ||
        //     $request->room[ 'account_uniq_id' ] == '6282217185656' ||
        //     $request->room[ 'account_uniq_id' ] == '628112249383' ||
        //     $request->room[ 'account_uniq_id' ] == '628977878980' ||
        //     $request->room[ 'account_uniq_id' ] == '6282114303110' ||
        //     $request->room[ 'account_uniq_id' ] == '6285693387995'
        // ) {

        $text_message = strtolower($request->text);

        if ($text_message == '#req_one_time_password') {
            $this->sendOneTimePassword($request);
        }

        /* update room id in profile */
        $profile = Profile::where('phone_number', $request->room['account_uniq_id'])->first();
        if ($profile != null) {
            $profile->room_id = $request->room['id'];
            $profile->save();
        }

        $keywordAccessBot = Parameter::where('key', 'keyword_bot')->first();

        /* handle access bot and greeting */
        if (strtolower($keywordAccessBot->value) == $text_message) {
            /* handle greeting */
            if ($profile != null) {
                $users = Users::with('role')->where('id', $profile->id_users)->first();

                if ($users->role->code == 'SADM') {
                    $greeting = WABotKeyword::where('keyword', 'greeting_super')->first();
                } else if ($users->role->code == 'ADM') {
                    $greeting = WABotKeyword::where('keyword', 'greeting_adm')->first();
                } else if ($users->role->code == 'APNT') {
                    $greeting = WABotKeyword::where('keyword', 'greeting_apnt')->first();
                } else if ($users->role->code == 'MMBR') {
                    $greeting = WABotKeyword::where('keyword', 'greeting_mmbr')->first();
                } else {
                    $greeting = WABotKeyword::where('keyword', 'greeting_general')->first();
                }
            } else {
                $greeting = WABotKeyword::where('keyword', 'greeting_general')->first();
            }

            $params = array(
                'wa_bot_keyword' => $greeting->keyword,
                'room_id'        => $request->room['id'],
                'type'           => $request->type,
                'text'           => str_replace('<br>', '\n', $greeting->question),
                'is_outside_bot' => false,
            );

            /* create chat and reply bot */
            $this->createHistoryChatFromCustomer($request, $params);
        } else {
            /* handle reply bot */
            $wa_chat_history = WAChatHistory::whereIn('from', [$this->wa_business_number, $request->room['account_uniq_id']])
                ->whereIn('to', [$request->room['account_uniq_id'], $this->wa_business_number])
                ->where('wa_bot_keyword', '!=', '-')
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($wa_chat_history != null) {
                $wabot_keyword = WABotKeyword::where('keyword', $wa_chat_history->wa_bot_keyword)->first();

                if ($request->participant_type == 'customer') {
                    /* handle chat from customer and reply bot */
                    $answer       = explode(',', str_replace(' ', '', strtolower($wabot_keyword->answer)));
                    $answer_index = array_search(preg_replace('/[^a-zA-Z0-9]/', '', strtolower($request->text)), $answer);

                    /* jika answer index tidak ketemu maka bernilai false */
                    if ($answer_index !== false) {
                        $next_question = explode(',', str_replace(' ', '', strtolower($wabot_keyword->next_question)));
                        if (count($next_question) > $answer_index) {
                            $next_question_index = $next_question[$answer_index];
                        } else {
                            $next_question_index = '-';
                        }

                        /* handel jika answer ada tapi next_question kosong atau interaksi dengan database */
                        if ($next_question_index !== '-') {
                            $wabot_next_question = WABotKeyword::where('id', $next_question_index)->first();

                            if ($wabot_next_question->keyword === 'register') {
                                /* get liga terbaru */
                                $liga_history = LigaHistory::where('deleted_at', null)->orderBy('date_start', 'DESC')->first();

                                $send_next_question = str_replace('<br>', "\n", $wabot_next_question->question);
                                $send_next_question = str_replace('{{1}}', $liga_history->id, $wabot_next_question->question);
                            } else if ($wabot_next_question->keyword === 'mission') {
                                /* get liga terbaru */
                                $liga_history = LigaHistory::where('deleted_at', null)->orderBy('date_start', 'DESC')->first();

                                /* get mission terbaru */
                                $type_misi = ['Harian', 'Mingguan', 'Bulanan', 'Triwulan', 'Semester', 'Tahunan'];

                                $mission = [];
                                foreach ($type_misi as $type) {
                                    $mission_data = LigaMission::where('type', $type)->where('id_liga_history', $liga_history->id)->get();

                                    if (count($mission_data) > 0) {
                                        array_push($mission, $mission_data);
                                    }
                                }

                                /* handle misi kosong */
                                if (count($mission) > 0) {
                                    $list_mission = [];
                                    foreach ($mission as $key => $item) {
                                        if (count($item) > 0) {
                                            array_push($list_mission, '- ' . $item[0]->misi);
                                        }
                                    }

                                    $mission_post       = implode("\n", $list_mission);
                                    $send_next_question = str_replace('<br>', "\n", $wabot_next_question->question);
                                    $send_next_question = str_replace('{{1}}', $mission_post, $send_next_question);
                                } else {
                                    $wabot_misi_notfound = WABotKeyword::where('keyword', 'mission_not_found')->first();
                                    $send_next_question  = str_replace('<br>', "\n", $wabot_misi_notfound->question);
                                }
                            } else if ($wabot_next_question->keyword === 'profile') {
                                /* handle update profile */
                                $profile = Profile::where('phone_number', $request->room['account_uniq_id'])->first();

                                $name      = $profile->name;
                                $twitter   = $profile->twitter_username;
                                $facebook  = $profile->facebook_username;
                                $tiktok    = $profile->tiktok_username;
                                $instagram = $profile->instagram_username;
                                $youtube   = $profile->youtube_username;

                                $send_next_question = str_replace('<br>', '\n', $wabot_next_question->question);
                                $send_next_question = str_replace('{{1}}', $name, $send_next_question);
                                $send_next_question = str_replace('{{2}}', $twitter, $send_next_question);
                                $send_next_question = str_replace('{{3}}', $facebook, $send_next_question);
                                $send_next_question = str_replace('{{4}}', $youtube, $send_next_question);
                                $send_next_question = str_replace('{{5}}', $tiktok, $send_next_question);
                                $send_next_question = str_replace('{{6}}', $instagram, $send_next_question);
                            } else if ($wabot_next_question->keyword === 'my_point') {
                                /* handle untuk poin saya */
                                $get_all_point = UsersPoint::select(DB::raw('COALESCE(SUM(point), 0) as all_point'))
                                    ->where('id_users', $profile->id_users)
                                    ->first();

                                $all_point_kreator = UsersPoint::select(DB::raw('COALESCE(SUM(point), 0) as all_point'))
                                    ->where('id_users', $profile->id_users)
                                    ->where('id_liga_history', null)
                                    ->first();

                                $get_recent_point = UsersPoint::select('point', 'title', 'id_liga_history')
                                    ->where('id_users', $profile->id_users)
                                    ->join('liga_history', 'liga_history.id', '=', 'users_point.id_liga_history')
                                    ->orderBy('users_point.created_at', 'DESC')
                                    ->first();

                                if ($get_recent_point != null) {
                                    $get_twitter_point = TwitterPoint::select(DB::raw('COALESCE(SUM(total_point), 0) as twitter_point'))
                                        ->where('id_users', $profile->id_users)
                                        ->where('id_liga_history', $get_recent_point->id_liga_history)
                                        ->first();

                                    $value_twitter_point = $get_twitter_point->twitter_point;

                                } else {
                                    $value_twitter_point = 0;
                                }

                                $all_point        = $get_all_point ? $get_all_point->all_point : 0;
                                $recent_point     = $get_recent_point ? $get_recent_point->point : 0;
                                $recent_name_liga = $get_recent_point ? $get_recent_point->title : 'Kreator';
                                $twitter_point    = $value_twitter_point;
                                $facebook_point   = 0;
                                $instagram_point  = 0;
                                $youtube_point    = 0;
                                $tiktok_point     = 0;
                                $kreator_point    = $all_point_kreator ? $all_point_kreator->all_point : 0;

                                $send_next_question = str_replace('<br>', "\n", $wabot_next_question->question);
                                $send_next_question = str_replace('{{1}}', $all_point, $send_next_question);
                                $send_next_question = str_replace('{{2}}', $kreator_point, $send_next_question);
                                $send_next_question = str_replace('{{3}}', $recent_name_liga, $send_next_question);
                                $send_next_question = str_replace('{{4}}', $recent_point, $send_next_question);
                                $send_next_question = str_replace('{{5}}', $twitter_point, $send_next_question);
                                $send_next_question = str_replace('{{6}}', $facebook_point, $send_next_question);
                                $send_next_question = str_replace('{{7}}', $instagram_point, $send_next_question);
                                $send_next_question = str_replace('{{8}}', $tiktok_point, $send_next_question);
                                $send_next_question = str_replace('{{9}}', $youtube_point, $send_next_question);
                            } else if ($wabot_next_question->keyword === 'call_agent') {
                                /* handel call agent diluar jam kerja */
                                $hours = date('H');

                                if ($hours >= 9 && $hours <= 17) {
                                    /* jam kerja */
                                    $send_next_question = str_replace('<br>', "\n", $wabot_next_question->question);
                                } else {
                                    /* diluar jam kerja */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'call_agent_operational')->first();
                                    $send_next_question  = str_replace('<br>', "\n", $wabot_next_question->question);
                                }
                            } else {
                                /* general reply */
                                $send_next_question = str_replace('<br>', "\n", $wabot_next_question->question);
                            }

                            /* create chat and reply bot */
                            $params = array(
                                'wa_bot_keyword' => $wabot_next_question->keyword,
                                'room_id'        => $request->room['id'],
                                'type'           => $request->type,
                                'text'           => $send_next_question,
                                'is_outside_bot' => false,
                            );
                            $this->createHistoryChatFromCustomer($request, $params);
                        } else {
                            /* handle next_question - */
                            if ($wabot_keyword->keyword === 'standings') {
                                /* handle klasemen */
                                $options = array('Kesatria', 'Kreator', 'DPC', 'DPD', 'DPW');

                                $is_index            = ($request->text - 1);
                                $answer_index        = $options[$is_index];
                                $wabot_next_question = WABotKeyword::where('keyword', 'standings_reply')->first();

                                if ($answer_index == 'Kesatria') {
                                    /* get data kesatria */
                                    $kesatria = Users::selectRaw('
                                            ROW_NUMBER () OVER (order by SUM(users_point.point) desc) AS row, profile.name, sum(users_point.point) total_point,
                                            (select sum(point) from creator_point where id_users = users.id) as creator_point,
                                            (select sum(total_point) from twitter_point where id_users = users.id group by id_users) as twitter_point,
                                            0 as instagram_point, 0 as facebook_point, 0 as tiktok_point, 0 as youtube_point')
                                        ->join('users_point', 'users_point.id_users', '=', 'users.id')
                                        ->join('profile', 'profile.id_users', '=', 'users.id')
                                        ->groupByRaw('users.id, profile.name')
                                        ->orderByRaw('total_point desc')
                                        ->limit(50)
                                        ->get();

                                    $list_kesatria = [];
                                    foreach ($kesatria as $key => $kesatria) {

                                        array_push($list_kesatria, ($key + 1) . '. ' . $kesatria->name . ' : ' . $kesatria->total_point);

                                        if ($key == 9 || $key == 19 || $key == 29 || $key == 39) {
                                            array_push($list_kesatria, "<br>");
                                        }

                                    }

                                    $list_kesatria      = str_replace('<br>', '', $list_kesatria);
                                    $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                    $send_next_question = str_replace('{{1}}', implode("\n", $list_kesatria), $send_next_question);

                                } else if ($answer_index == 'Kreator') {
                                    /* get data kreator */
                                    $kreator = Users::selectRaw('ROW_NUMBER () OVER (ORDER BY creator_point.point desc) AS row, profile.name,
                                            creator_point.point as total_point')
                                        ->join('profile', 'profile.id_users', '=', 'users.id')
                                        ->join('creator_point', 'creator_point.id_users', '=', 'users.id')
                                        ->orderBy('total_point', 'desc')
                                        ->limit(20)
                                        ->get();

                                    $list_kreator = [];
                                    foreach ($kreator as $key => $kreator) {

                                        array_push($list_kreator, ($key + 1) . '. ' . $kreator->name . ' : ' . $kreator->total_point);

                                        if ($key == 9 || $key == 19 || $key == 29 || $key == 39) {
                                            array_push($list_kreator, "<br>");
                                        }

                                    }

                                    $list_kreator       = str_replace('<br>', '', $list_kreator);
                                    $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                    $send_next_question = str_replace('{{1}}', implode("\n", $list_kreator), $send_next_question);

                                } else if ($answer_index == 'DPC') {
                                    /* get data dpc / kecamatan */
                                    $dpc = Users::selectRaw('ROW_NUMBER () OVER (ORDER BY SUM(users_point.point) desc) AS row, id_districts.name, SUM(users_point.point) AS total_point ')
                                        ->join('users_point', function ($join) {
                                            $join->on('users_point.id_users', '=', 'users.id');
                                        })
                                        ->leftJoin('id_districts', 'id_districts.id', '=', 'users.kecamatan')
                                        ->where('id_district.ranker_at_dpc', 1)
                                        ->groupBy('id_districts.name')
                                        ->orderBy('total_point', 'desc')
                                        ->limit(20)
                                        ->get();

                                    $list_dpc = [];
                                    foreach ($dpc as $key => $item) {

                                        array_push($list_dpc, ($key + 1) . '. ' . $item->name . ' : ' . $item->total_point);

                                        if ($key == 9) {
                                            array_push($list_dpc, "<br>");
                                        }

                                    }

                                    $list_dpc           = str_replace('<br>', '', $list_dpc);
                                    $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                    $send_next_question = str_replace('{{1}}', implode("\n", $list_dpc), $send_next_question);

                                } else if ($answer_index == 'DPD') {
                                    /* get data dpd / kota */
                                    $dpd = Users::selectRaw("ROW_NUMBER () OVER (ORDER BY SUM(users_point.point) desc) AS row, SUM(users_point.point) AS total_point, id_cities.name")
                                        ->join('users_point', function ($join) {
                                            $join->on('users_point.id_users', '=', 'users.id');
                                        })
                                        ->leftJoin('id_cities', 'id_cities.id', '=', 'users.kota_kab')
                                        ->where('id_cities.ranker_at_dpd', 1)
                                        ->groupBy('id_cities.name')
                                        ->orderBy('total_point', 'desc')
                                        ->limit(20)
                                        ->get();

                                    $list_dpd = [];
                                    foreach ($dpd as $key => $item) {

                                        array_push($list_dpd, ($key + 1) . '. ' . $item->name . ' : ' . $item->total_point);

                                        if ($key == 9) {
                                            array_push($list_dpd, "<br>");
                                        }

                                    }

                                    $list_dpd           = str_replace('<br>', '', $list_dpd);
                                    $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                    $send_next_question = str_replace('{{1}}', implode("\n", $list_dpd), $send_next_question);

                                } else if ($answer_index == 'DPW') {
                                    /* get data dpw / provinsi */
                                    $dpw = Users::selectRaw("ROW_NUMBER () OVER (ORDER BY SUM(users_point.point) desc) AS row, SUM(users_point.point) AS total_point, id_provinces.name")
                                        ->join('users_point', function ($join) {
                                            $join->on('users_point.id_users', '=', 'users.id');
                                        })
                                        ->join('id_provinces', 'id_provinces.id', '=', 'users.provinsi')
                                        ->where('id_provinces.ranker_at_dpw', 1)
                                        ->groupBy('id_provinces.name')
                                        ->orderBy('total_point', 'desc')
                                        ->limit(20)
                                        ->get();

                                    $list_dpw = [];
                                    foreach ($dpw as $key => $item) {

                                        array_push($list_dpw, ($key + 1) . '. ' . $item->name . ' : ' . $item->total_point);

                                        if ($key == 9) {
                                            array_push($list_dpw, "<br>");
                                        }

                                    }

                                    $list_dpw           = str_replace('<br>', '', $list_dpw);
                                    $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                    $send_next_question = str_replace('{{1}}', implode("\n", $list_dpw), $send_next_question);
                                }
                            }

                            /* create chat and reply bot */
                            $params = array(
                                'wa_bot_keyword' => $wabot_next_question->keyword,
                                'room_id'        => $request->room['id'],
                                'type'           => $request->type,
                                'text'           => $send_next_question,
                                'is_outside_bot' => false,
                            );
                            $this->createHistoryChatFromCustomer($request, $params);
                        }
                    } else {
                        /* handle interaksi database */
                        if ($wabot_keyword->answer === 'interaksi_database') {
                            $is_profile_update = false;
                            if (strpos($wabot_keyword->keyword, 'profile') !== false) {
                                $is_profile_update = true;
                            }

                            $is_profile_bot_not_found = false;
                            if (strpos($wabot_keyword->keyword, 'bot_not_found_profile') !== false) {
                                $is_profile_bot_not_found = true;
                            }

                            if ($is_profile_update === true && $is_profile_bot_not_found === false) {
                                /* handle update profile */
                                $profile = Profile::where('phone_number', $request->room['account_uniq_id'])->first();

                                if ($wabot_keyword->keyword == 'profile_name') {
                                    $profile->name = $request->text;
                                } else if ($wabot_keyword->keyword == 'profile_twitter') {
                                    $profile->twitter_username = $request->text;
                                    $profile->twitter_id       = '';
                                } else if ($wabot_keyword->keyword == 'profile_facebook') {
                                    $profile->facebook_username = $request->text;
                                    $profile->facebook_id       = '';
                                } else if ($wabot_keyword->keyword == 'profile_youtube') {
                                    $profile->youtube_username = $request->text;
                                    $profile->youtube_id       = '';
                                } else if ($wabot_keyword->keyword == 'profile_tiktok') {
                                    $profile->tiktok_username = $request->text;
                                    $profile->tiktok_id       = '';
                                } else if ($wabot_keyword->keyword == 'profile_instagram') {
                                    $profile->instagram_username = $request->text;
                                    $profile->instagram_id       = '';
                                }

                                $profile->save();

                                $profile = Profile::where('phone_number', $request->room['account_uniq_id'])->first();

                                $name      = $profile->name;
                                $twitter   = $profile->twitter_username;
                                $facebook  = $profile->facebook_username;
                                $tiktok    = $profile->tiktok_username;
                                $instagram = $profile->instagram_username;
                                $youtube   = $profile->youtube_username;

                                $wabot_next_question = WABotKeyword::where('keyword', 'profile')->first();
                                $send_next_question  = str_replace("<br>", "\n", $wabot_next_question->question);
                                $send_next_question  = str_replace('{{1}}', $name, $send_next_question);
                                $send_next_question  = str_replace('{{2}}', $twitter, $send_next_question);
                                $send_next_question  = str_replace('{{3}}', $facebook, $send_next_question);
                                $send_next_question  = str_replace('{{4}}', $youtube, $send_next_question);
                                $send_next_question  = str_replace('{{5}}', $tiktok, $send_next_question);
                                $send_next_question  = str_replace('{{6}}', $instagram, $send_next_question);

                                /* create chat and reply bot */
                                $params = array(
                                    'wa_bot_keyword' => $wabot_next_question->keyword,
                                    'room_id'        => $request->room['id'],
                                    'type'           => $request->type,
                                    'text'           => $send_next_question,
                                    'is_outside_bot' => false,
                                );

                                $this->createHistoryChatFromCustomer($request, $params);
                            } else {

                                /* handle chat diluar bot */
                                if ($wabot_keyword->keyword === 'bot_not_found_greeting') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_super')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_greeting_next') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_super_next')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_greeting_adm') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_adm')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_greeting_adm_next') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_adm_next')->first();
                                }if ($wabot_keyword->keyword === 'bot_not_found_greeting_apnt') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_apnt')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_greeting_apnt_next') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_apnt_next')->first();
                                }if ($wabot_keyword->keyword === 'bot_not_found_greeting_mmbr') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_mmbr')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_greeting_mmbr_next') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_mmbr_next')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_greeting_general') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_general')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_greeting_general_next') {

                                    /* handle answer greeting diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'greeting_general_next')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_guide') {

                                    /* handle answer guide diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'guide')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_register') {

                                    /* handle answer guide diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'register')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_standings') {

                                    /* handle answer guide diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'standings')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_standings_reply') {

                                    /* handle answer guide diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'standings_reply')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_mission') {

                                    /* handle answer guide diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'mission')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_profile') {

                                    /* handle answer guide diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'profile')->first();
                                } else if ($wabot_keyword->keyword === 'bot_not_found_my_point') {

                                    /* handle answer guide diluar answer bot */
                                    $wabot_next_question = WABotKeyword::where('keyword', 'my_point')->first();
                                }

                                /* cek answer */
                                $answer       = explode(',', str_replace(' ', '', strtolower($wabot_next_question->answer)));
                                $answer_index = array_search(preg_replace('/[^a-zA-Z0-9]/', '', strtolower($request->text)), $answer);

                                if ($answer_index !== false) {
                                    $next_question = explode(',', str_replace(' ', '', strtolower($wabot_next_question->next_question)));
                                    if (count($next_question) > $answer_index) {
                                        $next_question_index = $next_question[$answer_index];
                                    } else {
                                        $next_question_index = '-';
                                    }

                                    if ($next_question_index !== '-') {
                                        $wabot_next_question = WABotKeyword::where('id', $next_question_index)->first();
                                        $send_next_question  = str_replace('<br>', '\n', $wabot_next_question->question);
                                        $params              = array(
                                            'wa_bot_keyword' => $wabot_next_question->keyword,
                                            'room_id'        => $request->room['id'],
                                            'type'           => $request->type,
                                            'text'           => $send_next_question,
                                            'is_outside_bot' => false,
                                        );
                                    } else {

                                        /* handle next_question - */
                                        if ($wabot_next_question->keyword === 'standings') {
                                            /* handle klasemen */
                                            $options = array('Kesatria', 'Kreator', 'DPC', 'DPD', 'DPW');

                                            $is_index            = ($request->text - 1);
                                            $answer_index        = $options[$is_index];
                                            $wabot_next_question = WABotKeyword::where('keyword', 'standings_reply')->first();

                                            if ($answer_index == 'Kesatria') {
                                                /* get data kesatria */
                                                $kesatria = Users::selectRaw('
                                                        ROW_NUMBER () OVER (order by SUM(users_point.point) desc) AS row, profile.name, sum(users_point.point) total_point,
                                                        (select sum(point) from creator_point where id_users = users.id) as creator_point,
                                                        (select sum(total_point) from twitter_point where id_users = users.id group by id_users) as twitter_point,
                                                        0 as instagram_point, 0 as facebook_point, 0 as tiktok_point, 0 as youtube_point')
                                                    ->join('users_point', 'users_point.id_users', '=', 'users.id')
                                                    ->join('profile', 'profile.id_users', '=', 'users.id')
                                                    ->groupByRaw('users.id, profile.name')
                                                    ->orderByRaw('total_point desc')
                                                    ->limit(50)
                                                    ->get();

                                                $list_kesatria = [];
                                                foreach ($kesatria as $key => $kesatria) {

                                                    array_push($list_kesatria, ($key + 1) . '. ' . $kesatria->name . ' : ' . $kesatria->total_point);

                                                    if ($key == 9 || $key == 19 || $key == 29 || $key == 39) {
                                                        array_push($list_kesatria, "<br>");
                                                    }

                                                }

                                                $list_kesatria      = str_replace('<br>', '', $list_kesatria);
                                                $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                                $send_next_question = str_replace('{{1}}', implode("\n", $list_kesatria), $send_next_question);

                                            } else if ($answer_index == 'Kreator') {
                                                /* get data kreator */
                                                $kreator = Users::selectRaw('ROW_NUMBER () OVER (ORDER BY creator_point.point desc) AS row, profile.name,
                                                        creator_point.point as total_point')
                                                    ->join('profile', 'profile.id_users', '=', 'users.id')
                                                    ->join('creator_point', 'creator_point.id_users', '=', 'users.id')
                                                    ->orderBy('total_point', 'desc')
                                                    ->limit(20)
                                                    ->get();

                                                $list_kreator = [];
                                                foreach ($kreator as $key => $kreator) {

                                                    array_push($list_kreator, ($key + 1) . '. ' . $kreator->name . ' : ' . $kreator->total_point);

                                                    if ($key == 9 || $key == 19 || $key == 29 || $key == 39) {
                                                        array_push($list_kreator, "\n");
                                                    }

                                                }

                                                $list_kreator       = str_replace('<br>', '', $list_kreator);
                                                $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                                $send_next_question = str_replace('{{1}}', implode("\n", $list_kreator), $send_next_question);

                                            } else if ($answer_index == 'DPC') {
                                                /* get data dpc / kecamatan */
                                                $dpc = Users::selectRaw('ROW_NUMBER () OVER (ORDER BY SUM(users_point.point) desc) AS row, id_districts.name, SUM(users_point.point) AS total_point ')
                                                    ->join('users_point', function ($join) {
                                                        $join->on('users_point.id_users', '=', 'users.id');
                                                    })
                                                    ->leftJoin('id_districts', 'id_districts.id', '=', 'users.kecamatan')
                                                    ->groupBy('id_districts.name')
                                                    ->orderBy('total_point', 'desc')
                                                    ->limit(20)
                                                    ->get();

                                                $list_dpc = [];
                                                foreach ($dpc as $key => $item) {

                                                    array_push($list_dpc, ($key + 1) . '. ' . $item->name . ' : ' . $item->total_point);

                                                    if ($key == 9) {
                                                        array_push($list_dpc, "<br>");
                                                    }

                                                }

                                                $list_dpc           = str_replace('<br>', '', $list_dpc);
                                                $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                                $send_next_question = str_replace('{{1}}', implode("\n", $list_dpc), $send_next_question);

                                            } else if ($answer_index == 'DPD') {
                                                /* get data dpd / kota */
                                                $dpd = Users::selectRaw("ROW_NUMBER () OVER (ORDER BY SUM(users_point.point) desc) AS row, SUM(users_point.point) AS total_point, id_cities.name")
                                                    ->join('users_point', function ($join) {
                                                        $join->on('users_point.id_users', '=', 'users.id');
                                                    })
                                                    ->leftJoin('id_cities', 'id_cities.id', '=', 'users.kota_kab')
                                                    ->groupBy('id_cities.name')
                                                    ->orderBy('total_point', 'desc')
                                                    ->limit(20)
                                                    ->get();

                                                $list_dpd = [];
                                                foreach ($dpd as $key => $item) {

                                                    array_push($list_dpd, ($key + 1) . '. ' . $item->name . ' : ' . $item->total_point);

                                                    if ($key == 9) {
                                                        array_push($list_dpd, "<br>");
                                                    }

                                                }

                                                $list_dpd           = str_replace('<br>', '', $list_dpd);
                                                $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                                $send_next_question = str_replace('{{1}}', implode("\n", $list_dpd), $send_next_question);

                                            } else if ($answer_index == 'DPW') {
                                                /* get data dpw / provinsi */
                                                $dpw = Users::selectRaw("ROW_NUMBER () OVER (ORDER BY SUM(users_point.point) desc) AS row, SUM(users_point.point) AS total_point, id_provinces.name")
                                                    ->join('users_point', function ($join) {
                                                        $join->on('users_point.id_users', '=', 'users.id');
                                                    })
                                                    ->join('id_provinces', 'id_provinces.id', '=', 'users.provinsi')
                                                    ->groupBy('id_provinces.name')
                                                    ->orderBy('total_point', 'desc')
                                                    ->limit(20)
                                                    ->get();

                                                $list_dpw = [];
                                                foreach ($dpw as $key => $item) {

                                                    array_push($list_dpw, ($key + 1) . '. ' . $item->name . ' : ' . $item->total_point);

                                                    if ($key == 9) {
                                                        array_push($list_dpw, "<br>");
                                                    }

                                                }

                                                $list_dpw           = str_replace('<br>', '', $list_dpw);
                                                $send_next_question = str_replace('{{0}}', $answer_index, $wabot_next_question->question);
                                                $send_next_question = str_replace('{{1}}', implode("\n", $list_dpw), $send_next_question);
                                            }
                                        }

                                        /* create chat and reply bot */
                                        $params = array(
                                            'wa_bot_keyword' => $wabot_next_question->keyword,
                                            'room_id'        => $request->room['id'],
                                            'type'           => $request->type,
                                            'text'           => $send_next_question,
                                            'is_outside_bot' => false,
                                        );
                                        $this->createHistoryChatFromCustomer($request, $params);
                                    }
                                } else {

                                    /* handle answer diluar bot jika bernilai false */
                                    $send_next_question = str_replace('<br>', "\n", $wabot_keyword->question);
                                    $params             = array(
                                        'wa_bot_keyword' => $wabot_next_question->keyword,
                                        'room_id'        => $request->room['id'],
                                        'type'           => $request->type,
                                        'text'           => $send_next_question,
                                        'is_outside_bot' => true,
                                    );
                                }

                                /* create chat and reply bot */
                                $this->createHistoryChatFromCustomer($request, $params);
                            }

                        } else {
                            /* handle answer greeting diluar answer bot */
                            if ($wabot_keyword->keyword == 'greeting_supper') {
                                $type_keyword = 'bot_not_found_greeting';
                            } else if ($wabot_keyword->keyword == 'greeting_super_next') {
                                $type_keyword = 'bot_not_found_greeting_next';
                            } else if ($wabot_keyword->keyword == 'greeting_adm') {
                                $type_keyword = 'bot_not_found_greeting_adm';
                            } else if ($wabot_keyword->keyword == 'greeting_adm_next') {
                                $type_keyword = 'bot_not_found_greeting_adm_next';
                            } else if ($wabot_keyword->keyword == 'greeting_apnt') {
                                $type_keyword = 'bot_not_found_greeting_apnt';
                            } else if ($wabot_keyword->keyword == 'greeting_apnt_next') {
                                $type_keyword = 'bot_not_found_greeting_apnt_next';
                            } else if ($wabot_keyword->keyword == 'greeting_mmbr') {
                                $type_keyword = 'bot_not_found_greeting_mmbr';
                            } else if ($wabot_keyword->keyword == 'greeting_mmbr_next') {
                                $type_keyword = 'bot_not_found_greeting_mmbr_next';
                            } else if ($wabot_keyword->keyword == 'greeting_general') {
                                $type_keyword = 'bot_not_found_greeting_general';
                            } else if ($wabot_keyword->keyword == 'greeting_general_next') {
                                $type_keyword = 'bot_not_found_greeting_general_next';
                            } else if ($wabot_keyword->keyword == 'guide') {
                                $type_keyword = 'bot_not_found_guide';
                            } else if ($wabot_keyword->keyword == 'register') {
                                $type_keyword = 'bot_not_found_register';
                            } else if ($wabot_keyword->keyword == 'standings') {
                                $type_keyword = 'bot_not_found_standings';
                            } else if ($wabot_keyword->keyword == 'standings_reply') {
                                $type_keyword = 'bot_not_found_standings_reply';
                            } else if ($wabot_keyword->keyword == 'mission') {
                                $type_keyword = 'bot_not_found_mission';
                            } else if ($wabot_keyword->keyword == 'profile') {
                                $type_keyword = 'bot_not_found_profile';
                            } else if ($wabot_keyword->keyword == 'my_point') {
                                $type_keyword = 'bot_not_found_my_point';
                            } else {
                                $type_keyword = 'general_chat';
                            }

                            if ($type_keyword !== 'general_chat') {
                                $wabot_not_found    = WABotKeyword::where('keyword', $type_keyword)->first();
                                $send_next_question = str_replace('<br>', "\n", $wabot_not_found->question);

                                /* create chat and reply bot */
                                $params = array(
                                    'wa_bot_keyword' => $wabot_not_found->keyword,
                                    'room_id'        => $request->room['id'],
                                    'type'           => $request->type,
                                    'text'           => $send_next_question,
                                    'is_outside_bot' => true,
                                );

                                $this->createHistoryChatFromCustomer($request, $params);
                            } else {
                                /* handle general chat */
                                $params = array(
                                    'wa_bot_keyword' => '-',
                                    'text'           => str_replace("\n", '<br>', $request->text),
                                    'is_outside_bot' => 0,
                                );

                                /* create wa chat history */
                                $this->createHistoryChatFromAgentOrBot($request, $params);
                            }
                        }
                    }

                } else {
                    $wa_chat_history = WAChatHistory::whereIn('from', [$this->wa_business_number, $request->room['account_uniq_id']])
                        ->whereIn('to', [$request->room['account_uniq_id'], $this->wa_business_number])
                        ->orderBy('created_at', 'DESC')
                        ->first();

                    /* handle chat from bot or agent and not reply bot */
                    if ($wa_chat_history->wa_bot_keyword != '-') {
                        $params = array(
                            'wa_bot_keyword' => $wabot_keyword->keyword,
                            'text'           => str_replace("\n", '<br>', $request->text),
                            'is_outside_bot' => 0,
                        );
                    } else {
                        $params = array(
                            'wa_bot_keyword' => '-',
                            'text'           => str_replace("\n", '<br>', $request->text),
                            'is_outside_bot' => 0,
                        );
                    }

                    /* create wa chat history */
                    $this->createHistoryChatFromAgentOrBot($request, $params);
                }
            }
        }

        return response()->json([
            'message' => 'Success receive webhook',
        ], 200);
        // } else {
        //     return response()->json( [
        //         'message' => 'Failed receive webhook',
        //     ], 500 );
        // }
    }

    private function createHistoryChatFromCustomer(Request $request, $params)
    {
        $starter_chat = $this->checkStarterChat($this->wa_business_number, $request->room['account_uniq_id']);

        $wa_chat_history     = new WAChatHistory();
        $wa_chat_history->id = Str::uuid();
        if ($request->participant_type == 'customer') {
            $wa_chat_history->from = $request->room['account_uniq_id'];
            $wa_chat_history->to   = $this->wa_business_number;
            $wa_chat_history->read = 0;
        } else if ($request->participant_type == 'agent' || $request->participant_type == 'bot') {
            $wa_chat_history->from = $this->wa_business_number;
            $wa_chat_history->to   = $request->room['account_uniq_id'];
            $wa_chat_history->read = 1;
        }
        $wa_chat_history->message_id     = $request->id;
        $wa_chat_history->message        = $request->text;
        $wa_chat_history->wa_bot_keyword = $params['wa_bot_keyword'];
        $wa_chat_history->type           = $request->type;
        $wa_chat_history->starter_chat   = $starter_chat;
        $wa_chat_history->is_outside_bot = $params['is_outside_bot'];
        $wa_chat_history->save();

        if ($params['wa_bot_keyword'] != '-') {
            /* send auto reply chat */
            $this->sendReplyBot($request, $params);
            // $this->createHistoryChatFromAgentOrBot($request, $params);
        }

        return $wa_chat_history;
    }

    private function createHistoryChatFromAgentOrBot(Request $request, $params)
    {
        $wa_bot_keyword = WabotKeyword::where('keyword', $params['wa_bot_keyword'])->first();
        $starter_chat   = $this->checkStarterChat($this->wa_business_number, $request->room['account_uniq_id']);

        $wa_chat_history     = new WAChatHistory();
        $wa_chat_history->id = Str::uuid();

        if ($request->participant_type == 'customer') {
            $wa_chat_history->from = $request->room['account_uniq_id'];
            $wa_chat_history->to   = $this->wa_business_number;
            $wa_chat_history->read = 0;
        } else if ($request->participant_type == 'agent' || $request->participant_type == 'bot') {
            $wa_chat_history->from = $this->wa_business_number;
            $wa_chat_history->to   = $request->room['account_uniq_id'];
            $wa_chat_history->read = 1;
        }

        $wa_chat_history->message_id     = $request->id;
        $wa_chat_history->message        = $params['text'];
        $wa_chat_history->wa_bot_keyword = $params['wa_bot_keyword'];
        $wa_chat_history->type           = $request->type;
        $wa_chat_history->starter_chat   = $starter_chat;
        $wa_chat_history->is_outside_bot = $params['is_outside_bot'];
        $wa_chat_history->save();

        return $wa_chat_history;
    }

    private function sendReplyBot($request, $params)
    {
        $send = $this->client->post($this->url . '/messages/whatsapp', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => $this->authentication_whatsapp_token,
            ],
            'json'    => $params,
        ]);
        $send = json_decode($send->getBody()->getContents());

        return $send;
    }

    private function checkStarterChat($from, $to)
    {
        $tday = date('Y-m-d H:i:s');
        $yday = date('Y-m-d H:i:s', strtotime('-1 day'));

        $cek = WAChatHistory::whereIn('from', [$from, $to])->whereIn('to', [$from, $to])
            ->whereBetween('created_at', [$yday, $tday])
            ->where('starter_chat', 1)
            ->first();

        if ($cek) {
            return 0;
        } else {
            return 1;
        }

    }

    private function sendOneTimePassword(Request $request)
    {
        $isProfile = Profile::where('phone_number', $request->room['account_uniq_id'])->first();

        if($isProfile != null) {
            $otp = $this->generateNumericOTP(6);

            $sendMessage = $this->client->post($this->url . "/broadcasts/whatsapp/direct", [
                "headers" => [
                    "Content-Type"  => "application/json",
                    "Authorization" => "Bearer " . $this->authentication_whatsapp_token,
                ],
                "json"    => [
                    "to_name"                => $request->room['name'],
                    "to_number"              => $request->room['account_uniq_id'],
                    "message_template_id"    => "6f6cdcce-f6d1-4c9a-8571-0e2435c8d01c",
                    "channel_integration_id" => "bb6c0882-8de4-49ab-b3c4-864c4adeebf6",
                    "language"               => [
                        "code" => "id",
                    ],
                    "parameters"             => [
                        "body" => [
                            [
                                "key"        => "1",
                                "value_text" => $otp,
                                "value"      => "otp_code_login",
                            ],
                        ],
                    ],
                ],
            ]);

            $sendMessage = json_decode($sendMessage->getBody()->getContents());

            /* create code otp whatsapp */
            if ($sendMessage) {
                $verifOtp               = new VerifOtp;
                $verifOtp->id           = Str::uuid();
                $verifOtp->phone_number = $request->room['account_uniq_id'];
                $verifOtp->otp_code     = $otp;
                $verifOtp->save();
            }
        }else{
            $params = array(
                'wa_bot_keyword' => '-',
                'room_id'        => $request->room['id'],
                'type'           => $request->type,
                'text'           => 'Mohon Maaf, Nomer kamu belum terdaftar',
                'is_outside_bot' => true,
            );

            $sendMessage = $this->sendReplyBot($request, $params);

            return $sendMessage;
        }

        return response()->json(['message' => 'Successfully otp login'], 200);
    }

    private function generateNumericOTP($n) {
      
        $generator = date('Ymd')."12348765";
      
        $result = "";
        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand()%(strlen($generator))), 1);
        }
      
        // Return result
        return $result;
    }
}
