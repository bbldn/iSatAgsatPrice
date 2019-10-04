<?php

namespace App\Other;

use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Kozz\Laravel\Facades\Guzzle;
use Sunra\PhpSimple\HtmlDomParser;


class Agsat
{
    protected $cookieKey = 'PHPSESSID';
    protected $guzzleDebug = false;
    protected $saveCookie = false;

    protected function login($saveCookie = true)
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

        $cookies = $this->parseCookies($response);
        if ($saveCookie) {
            $this->saveCookies($cookies);
        }

        return $cookies;
    }

    protected function parseCookies(Response $response)
    {
        $cookies = [];
        $matches = [];

        foreach ($response->getHeader('Set-Cookie') as $value) {
            preg_match('/([^=]+)=([^;]+);/', $value, $matches);
            $cookies[$matches[1]] = $matches[2];
        }

        return $cookies;
    }

    protected function saveCookies(array $cookies)
    {
        Cache::forever($this->cookieKey, json_encode($cookies));
    }

    public function getProducts(array $cookies = [])
    {
        if (count($cookies) == 0) {
            $cookies = SessionCookieJar::fromArray($this->login($this->saveCookie), 'www.agsat.com.ua');
        }

        $response = Guzzle::request('GET', 'https://www.agsat.com.ua/json/pricelist/', [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
            ],
            'debug' => $this->guzzleDebug,
            'cookies' => $cookies,
            'allow_redirects' => false,
        ]);

        if ($this->saveCookie) {
            $this->saveCookies($this->parseCookies($response));
        }

        return json_encode(strval($response->getBody()));
    }

    public function getDollarRate(array $cookies = [])
    {
        if (count($cookies) == 0) {
            $cookies = SessionCookieJar::fromArray($this->login($this->saveCookie), 'www.agsat.com.ua');
        }

        $response = Guzzle::request('GET', 'https://www.agsat.com.ua', [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
            ],
            'debug' => $this->guzzleDebug,
            'cookies' => $cookies,
            'allow_redirects' => false,
        ]);

        $html = strval($response->getBody());

        $dom = HtmlDomParser::str_get_html($html);

        $dollarRate = $dom->find('#top_right_nav li .navbar-text .siteprof_currency', 0)->text();

        $matches = [];
        preg_match('/^1\$ = (.+?) грн /', $dollarRate, $matches);

        return $matches[1];
    }
}
