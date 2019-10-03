<?php

namespace App\Http\Controllers;

use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Kozz\Laravel\Facades\Guzzle;

class TestController extends Controller
{
    protected $cookieKey = 'PHPSESSID';
    protected $guzzleDebug = false;

    protected function login()
    {
        $response = Guzzle::request('POST', 'https://www.agsat.com.ua/login/', [
            'form_params' => [
                'login' => env('ALOGIN'),
                'password' => env('APASSWORD'),
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',

            ],
            'debug' => $this->guzzleDebug,
            'allow_redirects' => false,
        ]);

        $this->setCookie($response);
    }

    protected function setCookie(Response $response)
    {
        $cookies = [];
        $arr = [];
        foreach ($response->getHeader('Set-Cookie') as $value) {
            preg_match('/([^=]+)=([^;]+);/', $value, $arr);
            $cookies[$arr[1]] = $arr[2];
        }

        Cache::forever($this->cookieKey, json_encode($cookies));
    }

    public function testAction()
    {
//        if (!Cache::has($this->cookieKey)) {
            $this->login();
//        }

        $cookies = SessionCookieJar::fromArray(json_decode(Cache::get($this->cookieKey), true), 'www.agsat.com.ua');

        $response = Guzzle::request('GET', 'https://www.agsat.com.ua/json/pricelist/', [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
            ],
            'debug' => $this->guzzleDebug,
            'cookies' => $cookies,
            'allow_redirects' => false,
        ]);
        $this->setCookie($response);
        echo strval($response->getBody());
//        dd(strval($response->getBody()));
//        dd($response->getHeader('Loca1tion'));
//
//
//
//        Cache::set('ttt', 12);
//        Cache::forget('ttt');
//        dd(Cache::get('ttt'));
    }
}
