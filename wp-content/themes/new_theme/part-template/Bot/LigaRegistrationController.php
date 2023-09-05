<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Bus;
use Carbon\Carbon;
use App\Models\AccessToken;
use App\Models\VerifOtp;
use App\Models\Parameter;
use App\Models\District;
use App\Models\City;
use App\Models\Province;
use App\Models\UsersModel as Users;
use App\Models\ProfileModel as Profile;
use App\Models\LigaHistoryParticipant as LigaParticipant;
use App\Models\LigaHistory;
use App\Models\WA\WAChatHistory;
use App\Jobs\SyncFollowingFollowersInBackground;
use DB;

class LigaRegistrationController extends Controller
{
    public function __Construct()
    {
        $this->client = new \GuzzleHttp\Client(['http_errors' => false]);
        $this->url = "https://service-chat.qontak.com/api/open/v1";
        $this->channel_integration_id = Parameter::where('key', 'whatsapp_integration_id')->first()->value;
        $this->authentication_whatsapp_token = Parameter::where('key', 'whatsapp_token')->first()->value;
        $this->wa_business_number = Parameter::where('key', 'wa_business_number')->first()->value;
    }

    public function authentication(Request $request)
    {
        $auth = $request->header('Authorization');
        $token = AccessToken::where('id', $auth)->first();

        $this->is_token = false;

        if($token != null)
            $this->is_token = true;

        return $this->is_token;
    }

    private function is_email_valid($email) {
        if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", trim($email))){
            return TRUE;
        }
        return FALSE;
    }

    public function registration(Request $request)
    {
        if($this->is_email_valid($request->email) == false) {
            return response()->json(['message' => 'Email tidak valid'], 400);
        }

        $phone_number = substr($request->phone_number, 0, 1); // cek validasi nomer

        if($phone_number == 0) {
            $phone_number = '62'.substr($request->phone_number, 1);
        }else{
            $phone_number = $request->phone_number;
        }

        $username = $request->username;
        if($this->isStringAllLowercase($username) == false) {
            return response()->json(['message' => 'Username diwajibkan menggunakan huruf kecil semua dan tidak boleh menggunakan simbol'], 400);
        }

        if(strlen($username) >= 20) {
            return response()->json(['message' => 'Username maksimal hanya 20 karakter saja'], 400);
        }

        if($this->validateUsername($username) == false) {
            return response()->json(['message' => 'Username tidak boleh mengandung spasi dan simbol'], 400);
        }

        $password = $request->password;
        $email = $request->email;
        $name = $request->name ?? '';
        $twitter_username = $request->twitter_username;
        $facebook_username = $request->facebook_username;
        $instagram_username = $request->instagram_username;
        $youtube_username = $request->youtube_username;
        $tiktok_username = $request->tiktok_username;
        $id_liga_history = $request->id_liga_history;
        $kecamatan = $request->kecamatan;
        $kota_kab = $request->kota_kab;
        $provinsi = $request->provinsi;
        $url_verif_email = $request->url_verif_email;
        $isLogin = $request->is_login;

        $message = '';
        $status = '';

        if($isLogin == 0) {
            $checkTwitter = Profile::where('twitter_username', $twitter_username)->where('deleted_at', null)->first();
            if($checkTwitter != null) {
                $message = 'Twitter sudah terdaftar';
                $status = 400;

                return response()->json(['message' => $message], $status);
            }

            // $checkFacebook = Profile::where('facebook_username', $facebook_username)->where('deleted_at', null)->first();
            // if($checkFacebook != null) {
            //     $message = 'Facebook sudah terdaftar';
            //     $status = 400;

            //     return response()->json(['message' => $message], $status);
            // }

            // $checkInstagram = Profile::where('instagram_username', $instagram_username)->where('deleted_at', null)->first();
            // if($checkInstagram != null) {
            //     $message = 'Instagram sudah terdaftar';
            //     $status = 400;

            //     return response()->json(['message' => $message], $status);
            // }

            // $checkYoutube = Profile::where('youtube_username', $youtube_username)->where('deleted_at', null)->first();
            // if($checkYoutube != null) {
            //     $message = 'Youtube sudah terdaftar';
            //     $status = 400;

            //     return response()->json(['message' => $message], $status);
            // }

            // $checkTiktok = Profile::where('tiktok_username', $tiktok_username)->where('deleted_at', null)->first();
            // if($checkTiktok != null) {
            //     $message = 'Tikok sudah terdaftar';
            //     $status = 400;

            //     return response()->json(['message' => $message], $status);
            // }

            $checkWhatsapp = Profile::where('phone_number', $phone_number)->where('deleted_at', null)->first();
            if($checkWhatsapp != null) {
                $message = 'Nomor whatsapp sudah terdaftar';
                $status = 400;

                return response()->json(['message' => $message], $status);
            }

            $checkUsername = Users::where('username', $username)->where('deleted_at', null)->first();
            if($checkUsername != null) {
                $message = 'Username sudah terdaftar';
                $status = 400;

                return response()->json(['message' => $message], $status);
            }

            $checkEmail = Users::where('email', $email)->where('deleted_at', null)->first();
            if($checkEmail != null) {
                $message = 'Email sudah terdaftar';
                $status = 400;

                return response()->json(['message' => $message], $status);
            }

            $getUsers = Users::where('email', $email)->where('deleted_at', null)->first();

            if($getUsers != null) {
                $checkLigaHistoryParticipant = LigaParticipant::where('id_users', $getUsers->id)->where('id_liga_history', $id_liga_history)->first();

                if($checkLigaHistoryParticipant == null) {

                    $liga_participant = new LigaParticipant;
                    $liga_participant->id = Str::uuid();
                    $liga_participant->id_users = $getUsers->id;
                    $liga_participant->id_liga_history = $id_liga_history;
                    $liga_participant->save();

                }

                $this->calculateStandings($request);
                $this->updateRegionalRanker($id_liga_history, $kecamatan, $kota_kab, $provinsi);

                Bus::dispatch(new SyncFollowingFollowersInBackground($twitter_username));

                return response()->json([
                    'message' => 'Registrasi liga berhasil',
                ], 200);
            }else{

                $data_regis = [
                    'username' => $username,
                    'password' => $password,
                    'email' => $email,
                    'kecamatan' => $kecamatan,
                    'kota_kab' => $kota_kab,
                    'provinsi' => $provinsi,
                    'name' => $name,
                    'phone_number' => $phone_number,
                    'twitter_username' => $twitter_username,
                    'facebook_username' => $facebook_username,
                    'tiktok_username' => $tiktok_username,
                    'youtube_username' => $youtube_username,
                    'instagram_username' => $instagram_username,
                    'id_liga_history' => $id_liga_history,
                ];
                DB::transaction(function () use ($data_regis, $request) {
                    $users = new Users;
                    $users->id = Str::uuid();
                    $users->id_users_role = 'eb24a3cc-a53b-11ed-b9df-0242ac120003'; //member
                    $users->username = $data_regis['username'];
                    $users->password = Hash::make($data_regis['password']);
                    $users->email = $data_regis['email'];
                    $users->email_verified = false;
                    $users->rank = 'DPC';
                    $users->kecamatan = $data_regis['kecamatan'];
                    $users->kota_kab = $data_regis['kota_kab'];
                    $users->provinsi = $data_regis['provinsi'];
                    $users->save();

                    $profile = new Profile;
                    $profile->id = Str::uuid();
                    $profile->id_users = $users->id;
                    $profile->name = $data_regis['name'];
                    $profile->address = null; // optional
                    $profile->phone_number = $data_regis['phone_number'];
                    $profile->valid_wa_number = false;
                    $profile->date_of_birth = null;
                    $profile->twitter_username = $data_regis['twitter_username'];
                    $profile->twitter_id = '';
                    $profile->facebook_username = $data_regis['facebook_username'];
                    $profile->facebook_id = '';
                    $profile->tiktok_username = $data_regis['tiktok_username'];
                    $profile->tiktok_id = '';
                    $profile->youtube_username = $data_regis['youtube_username'];
                    $profile->youtube_id = '';
                    $profile->instagram_username = $data_regis['instagram_username'];
                    $profile->instagram_id = '';
                    $profile->gender = null;
                    $profile->save();

                    $liga_participant = new LigaParticipant;
                    $liga_participant->id = Str::uuid();
                    $liga_participant->id_users = $users->id;
                    $liga_participant->id_liga_history = $data_regis['id_liga_history'];
                    $liga_participant->save();

                    /* send otp for valid wa number dan email verification*/
                    $messagewa = '';
                    if($profile) {
                        $messageWa = $this->sendWhatsappOTP($request);
                        $this->sendEmailVerification($request, $users->id);
                    }

                    $this->calculateStandings($request);
                    $this->updateRegionalRanker($data_regis['id_liga_history'], $data_regis['kecamatan'], $data_regis['kota_kab'], $data_regis['provinsi']);

                    Bus::dispatch(new SyncFollowingFollowersInBackground($data_regis['twitter_username']));
                    
                    return response()->json([
                        'message' => 'Registrasi akun dan liga berhasil',
                        'message_wa' => $messageWa
                    ], 200);
                });
            }
        }else{
            $getUsers = Users::where('email', $email)->where('deleted_at', null)->first();

            $checkLigaHistoryParticipant = LigaParticipant::where('id_users', $getUsers->id)->where('id_liga_history', $id_liga_history)->first();

            if($checkLigaHistoryParticipant == null) {

                $liga_participant = new LigaParticipant;
                $liga_participant->id = Str::uuid();
                $liga_participant->id_users = $getUsers->id;
                $liga_participant->id_liga_history = $id_liga_history;
                $liga_participant->save();

                $message = 'Registrsi liga berhasil';
            }else{
                $message = 'Akun kamu sudah terdaftar di liga';
            }

            $this->calculateStandings($request);
            $this->updateRegionalRanker($id_liga_history, $kecamatan, $kota_kab, $provinsi);

            Bus::dispatch(new SyncFollowingFollowersInBackground($twitter_username));

            return response()->json([
                'message' => $message
            ], 200);
        }
    }

    public function updateRegionalRanker($id_liga, $kecamatan, $kota_kab, $provinsi)
    {
        $newestLiga = LigaHistory::query()->whereRaw('created_at IS NOT NULL')->orderBy('created_at', 'desc')->first();

        // ** Update DPC Ranker
        $cte = new \Staudenmeir\LaravelCte\Query\Builder(app('db')->connection());
        $queryRankerDPC = $cte
            ->select([
                'id_districts.id'
            ])
            ->withExpression('total_user_region', function($query) use ($kecamatan){
                $query->select([
                    'id_districts.id',
                    DB::raw('total_kader AS total_user')
                ])
                ->from('id_districts')
                ->where('id_districts.id', '=', $kecamatan);
            })
            ->withExpression('total_participant_liga', function($query) use ($id_liga, $kecamatan){
                $query->select([
                    'id_districts.id',
                    DB::raw('COUNT(users.id) AS total_user')
                ])
                ->from('liga_history_participant', 'part')
                ->join('users', 'users.id', 'part.id_users')
                ->join('id_districts', 'id_districts.id', '=', 'users.kecamatan')
                ->where('part.id_liga_history', '=', $id_liga)
                ->where('id_districts.id', '=', $kecamatan)
                ->groupBy('id_districts.id');
            })
            ->from('id_districts')
            ->join('total_user_region AS reg', 'reg.id', '=', 'id_districts.id')
            ->join('total_participant_liga AS participant', 'participant.id', '=', 'id_districts.id')
            ->where('id_districts.id', '=', $kecamatan)
            ->whereRaw('(participant.total_user::float / reg.total_user::float) >= 0.2')
            ->get();

        $rankerDPC = [];
        foreach($queryRankerDPC as $row){
            array_push($rankerDPC, $row->id);
        }
        $update_ranker = District::whereIn('id', $rankerDPC)
                                ->update([
                                    'ranker_at_dpc' => 1,
                                ]);


        // ** Update DPD Ranker
        $cte = new \Staudenmeir\LaravelCte\Query\Builder(app('db')->connection());
        $queryRankerDPD = $cte
            ->select([
                'id_cities.id'
            ])
            ->withExpression('total_dpc_region', function($query) use($kota_kab){
                $query->select([
                    'id_cities.id',
                    DB::raw('COUNT(id_districts.id) AS total_user')
                ])
                ->from('id_cities')
                ->join('id_districts', 'id_districts.city_code', 'id_cities.code')
                ->where('id_cities.id', '=', $kota_kab)
                ->groupBy('id_cities.id');
            })
            ->withExpression('total_ranker_dpc', function($query) use ($newestLiga, $kota_kab){
                $query->select([
                    'id_cities.id',
                    DB::raw('COUNT(id_districts.id) AS total_user')
                ])
                ->from('id_cities')
                ->join('id_districts', 'id_districts.city_code', 'id_cities.code')
                ->where('id_districts.ranker_at_dpc', 1)
                ->where('id_cities.id', '=', $kota_kab)
                ->groupBy('id_cities.id');
            })
            ->from('id_cities')
            ->join('total_dpc_region AS reg', 'reg.id', '=', 'id_cities.id')
            ->join('total_ranker_dpc AS participant', 'participant.id', '=', 'id_cities.id')
            ->where('id_cities.id', '=', $kota_kab)
            ->whereRaw('(participant.total_user::float / reg.total_user::float) >= 0.2')
            ->get();

        $rankerDPD = [];
        foreach($queryRankerDPD as $row){
            array_push($rankerDPD, $row->id);
        }
        $update_ranker = City::whereIn('id', $rankerDPD)
                            ->update([
                                'ranker_at_dpd' => 1,
                            ]);


        // ** Update DPW Ranker
        $cte = new \Staudenmeir\LaravelCte\Query\Builder(app('db')->connection());
        $queryRankerDPW = $cte
            ->select([
                'id_provinces.id'
            ])
            ->withExpression('total_dpd_region', function($query) use ($provinsi){
                $query->select([
                    'id_provinces.id',
                    DB::raw('COUNT(id_cities.id) AS total_user')
                ])
                ->from('id_provinces')
                ->join('id_cities', 'id_cities.province_code', 'id_provinces.code')
                ->where('id_provinces.id', '=', $provinsi)
                ->groupBy('id_provinces.id');
            })
            ->withExpression('total_ranker_dpd', function($query) use ($newestLiga, $provinsi){
                $query->select([
                    'id_provinces.id',
                    DB::raw('COUNT(id_cities.id) AS total_user')
                ])
                ->from('id_provinces')
                ->join('id_cities', 'id_cities.province_code', 'id_provinces.code')
                ->where('id_cities.ranker_at_dpd', 1)
                ->where('id_provinces.id', '=', $provinsi)
                ->groupBy('id_provinces.id');
            })
            ->from('id_provinces')
            ->join('total_dpd_region AS reg', 'reg.id', '=', 'id_provinces.id')
            ->join('total_ranker_dpd AS participant', 'participant.id', '=', 'id_provinces.id')
            ->where('id_provinces.id', '=', $provinsi)
            ->whereRaw('(participant.total_user::float / reg.total_user::float) >= 0.2')
            ->get();

        $rankerDPW = [];
        foreach($queryRankerDPW as $row){
            array_push($rankerDPW, $row->id);
        }
        $update_ranker = Province::whereIn('id', $rankerDPW)
                                ->update([
                                    'ranker_at_dpw' => 1,
                                ]);
    }

    public function sendWhatsappOTP(Request $request)
    {
        $otp = $this->generateNumericOTP(6); // 6 is total otp number
        $wa_number = substr($request->phone_number, 0, 1); //  ex: 62819220109990

        if($wa_number == 0) {
            $wa_number = '62'.substr($request->phone_number, 1);
        }else{
            $wa_number = '62'.substr($request->phone_number, 2);
        }

        $profile = Profile::with('users')->where('phone_number', $wa_number)->first();

        /* send otp code to customer */
        if($profile->valid_wa_number == 0 && $otp != null) {
            if($profile->name == ''){
                $name = $request->username;
            }else{
                $name = $profile->users->username;
            }

            $sendMessage = $this->client->post($this->url . "/broadcasts/whatsapp/direct", [
                "headers" => [
                    "Content-Type"  => "application/json",
                    "Authorization" => "Bearer " . $this->authentication_whatsapp_token,
                ],
                "json" => [
                    "to_name" => $name,
                    "to_number" => $wa_number,
                    "message_template_id" => "94d319f1-08ef-4cc2-9692-ac451ed46b1d",
                    "channel_integration_id" => "bb6c0882-8de4-49ab-b3c4-864c4adeebf6",
                    "language" => [
                        "code" => "id",
                    ],
                    "parameters" => [
                        "body" => [
                            [
                                "key" => "1",
                                "value_text" => "Kode OTP",
                                "value" => "title_code",
                            ],
                            [
                                "key" => "2",
                                "value_text" => $otp,
                                "value" => "otp_code",
                            ],
                            [
                                "key" => "3",
                                "value_text" => env('VIEW_URL') . "/pages/otp",
                                "value" => "otp_link",
                            ],
                        ]
                    ]
                ]
            ]);

            $sendMessage = json_decode($sendMessage->getBody()->getContents());

            /* create code otp whatsapp */
            if($sendMessage) {
                $verifOtp = new VerifOtp;
                $verifOtp->id = Str::uuid();
                $verifOtp->phone_number = $wa_number;
                $verifOtp->otp_code = $otp;
                $verifOtp->save();
            }

            $message = $sendMessage->data->message_template->body;
            $message = str_replace('{{1}}', $sendMessage->data->contact_extra->title_code, $message);
            $message = str_replace('{{2}}', $sendMessage->data->contact_extra->otp_code, $message);
            $message = str_replace('{{3}}', $sendMessage->data->contact_extra->otp_link, $message);

            $wa_chat_history = new WAChatHistory();
            $wa_chat_history->id = Str::uuid();
            $wa_chat_history->to = $wa_number;
            $wa_chat_history->from = $this->wa_business_number;
            $wa_chat_history->read = 0;
            $wa_chat_history->message_id = $sendMessage->data->id;
            $wa_chat_history->message = str_replace("\n",'<br>', $message);
            $wa_chat_history->wa_bot_keyword = '-';
            $wa_chat_history->type = 'text';
            $wa_chat_history->starter_chat = 1;
            $wa_chat_history->is_outside_bot = 1;
            $wa_chat_history->save();

            return $sendMessage;
        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Whatsapp number has been verified'
            ], 200);
        }
    }

    private function sendEmailVerification(Request $request, $user_id)
    {
        $checkUsers = Users::where('id', $user_id)->first();

        if($checkUsers->email_verified == 0) {
            $token = md5(date('Ymdhis'));
            $expired = Carbon::now();

            $access_token = new AccessToken;
            $access_token->id = $token;
            $access_token->id_users = $user_id;
            $access_token->expired_at = $expired->addDays(1);
            $access_token->save();

            $data = [
                'to' => $request->email,
                'subject' => 'Verifikasi Email',
                'name' => $request->name,
                'token_verif' => $access_token->id,
                'url_verif' => env('VIEW_URL').'/pages/berhasilPage?token_verif='.$access_token->id,
                'url' => env('APP_URL').'/email'
            ];

            Mail::send('email.verification_email', $data, function($message) use ($data) {
                $message->to($data['to']);
                $message->subject($data['subject']);
            });

            return response()->json([
                'status' => 200,
                'message' => 'Successfully send verify email'
            ], 200);
        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Email has been verified'
            ], 200);
        }
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

    public function calculateStandings(Request $request) {
        $dpc = $this->calculateStandingsDpc($request);
        $dpd = $this->calculateStandingsDpd($dpc->city_code);
        $dpw = $this->calculateStandingsDpw($dpd->province_code);

        return response()->json(array(
            'message'=> 'Success calculate standings',
            'dpc' => $dpc,
            'dpd' => $dpd,
            'dpw' => $dpw
        ), 200);
    }

    private function calculateStandingsDpc(Request $request)
    {
        $liga_participant = LigaParticipant::select(DB::raw('COUNT(users.id) as total_participant'), 'users.kecamatan')
                ->join('users', 'users.id', '=', 'liga_history_participant.id_users')
                ->join('liga_history', 'liga_history.id', '=', 'liga_history_participant.id_liga_history')
                ->where('liga_history.id', $request->id_liga_history)
                ->where('users.deleted_at', null)
                ->groupBy('users.kecamatan')
                ->first();

        $district = District::where('id', $liga_participant->kecamatan)->first();

        $total_kader = $district->total_kader;
        $total_participant = $liga_participant->total_participant;

        $total = $total_kader * (20/100);

        /* jika total participant lebih besar dari ketentuan nya */
        if($total_participant >= $total) {
            /* update ranker dpc jadi 1 */
            $dpc = District::find($liga_participant->kecamatan);
            $dpc->ranker_at_dpc = true;
            $dpc->save();
        }

        return $district;
    }

    private function calculateStandingsDpd($city_code)
    {
        $dpc_all = District::select(DB::raw('COUNT(id) as total'))->where('city_code', $city_code)->first();
        $dpc_true = District::select(DB::raw('COUNT(id) as total'))->where('city_code', $city_code)->where('ranker_at_dpc', true)->first();

        $total_district = $dpc_all->total;
        $total_participant = $dpc_true->total;

        $total = $total_district * (20/100);

        /* jika total kecamatan lebih besar dari ketentuan nya */
        if($total_participant >= $total) {
            /* update ranker dpc jadi 1 */
            $dpd = City::where('code', $city_code)->first();
            $dpd->ranker_at_dpd = true;
            $dpd->save();
        }

        $dpd = City::where('code', $city_code)->first();

        return $dpd;
    }

    public function calculateStandingsDpw($province_code)
    {
        $dpd_all = City::select(DB::raw('COUNT(id) as total'))->where('province_code', $province_code)->first();
        $dpd_true = City::select(DB::raw('COUNT(id) as total'))->where('province_code', $province_code)->where('ranker_at_dpd', true)->first();

        $total_city = $dpd_all->total;
        $total_participant = $dpd_true->total;

        $total = $total_city * (20/100);

        /* jika total kecamatan lebih besar dari ketentuan nya */
        if($total_participant >= $total) {
            /* update ranker dpc jadi 1 */
            $dpw = Province::where('code', $province_code)->first();
            $dpw->ranker_at_dpw = true;
            $dpw->save();
        }

        $dpw = Province::where('code', $province_code)->first();

        return $dpw;
    }

    protected function validateUsername($string) {
        // Ekspresi reguler untuk memeriksa username
        $pattern = '/^[a-zA-Z0-9_]+$/';

        // Memeriksa apakah username cocok dengan pola yang diberikan
        if (preg_match($pattern, $string)) {
            return true; // Username valid
        } else {
            return false; // Username tidak valid
        }
    }

    protected function isStringAllLowercase($string) {
        $string = preg_replace('/\P{L}+/u', '', $string);  // Menghapus semua simbol dari string
        return ctype_lower($string); // Memeriksa apakah string hanya terdiri dari huruf kecil
    }

}
