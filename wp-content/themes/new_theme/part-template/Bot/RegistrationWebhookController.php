<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parameter;

class RegistrationWebhookController extends Controller
{
    public function __construct()
    {
        $parameter = Parameter::where('key','whatsapp_token')->first();

        $this->authorization = 'Bearer '.$parameter->value;
    }

    public function registration(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $register = $client->put("https://service-chat.qontak.com/api/open/v1/message_interactions", [
            "headers" => [
                "Content-Type"  => "multipart/form-data",
                "Authorization" => 'Bearer '.$this->authorization
            ],
            "json" => [
                "receive_message_from_agent" => $request->receive_message_from_agent,
                "receive_message_from_customer" => $request->receive_message_from_customer,
                "status_message" => true,
                "url" => $request->webhook_url
            ]
        ]);
        $register = json_decode($register->getBody()->getContents());

        return response()->json([
            'message' => 'Successfully registration webhook url'
        ]);
    }
}
