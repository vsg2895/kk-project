<?php namespace Jakten\Services;

use GuzzleHttp\{Psr7, Client};
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\{Config, Log};

/**
 * Class SMS46elks
 * @package Jakten\Helpers
 */
class SMS46elks
{
    /**
     * SMS_URL constant
     */
    const SMS_GATEWAY = 'https://api.46elks.com/a1/SMS';

    /**
     * Implementation of sending SMS
     *
     * @param array $sms
     */
    public static function sendSMS(array $sms = [])
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Basic '.base64_encode(Config::get('sms46elks.username'). ':' . Config::get('sms46elks.password')),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        try {
            $client->request('POST', self::SMS_GATEWAY, [
                'headers' => $headers,
                'form_params' => $sms
            ]);
            Log::info("SMS to " . $sms['to'] . " has been successfully sent");
        } catch (RequestException $e) {
            Log::error("Error of request: " . Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::error("Error of response: " . Psr7\str($e->getResponse()));
            }
        }
    }
}