<?php

namespace App\Other;

use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Kozz\Laravel\Facades\Guzzle;
use KubAT\PhpSimple\HtmlDomParser;

class Agsat
{
    /** @var string $cookieKey */
    protected $cookieKey = 'PHPSESSID';

    /** @var bool $guzzleDebug */
    protected $guzzleDebug = false;

    /** @var bool $saveCookie */
    protected $saveCookie = false;

    /**
     * @param bool $saveCookie
     * @return array
     */
    protected function login(bool $saveCookie = true): array
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
        if (true === $saveCookie) {
            $this->saveCookies($cookies);
        }

        return $cookies;
    }

    /**
     * @param Response $response
     * @return array
     */
    protected function parseCookies(Response $response): array
    {
        $cookies = [];

        foreach ($response->getHeader('Set-Cookie') as $value) {
            preg_match('/([^=]+)=([^;]+);/', $value, $matches);
            $cookies[$matches[1]] = $matches[2];
        }

        return $cookies;
    }

    /**
     * @param array $cookies
     */
    protected function saveCookies(array $cookies): void
    {
        Cache::forever($this->cookieKey, json_encode($cookies));
    }

    /**
     * @param array $cookies
     * @return false|string
     */
    public function getAll(array $cookies = []): string
    {
        if (0 === count($cookies)) {
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

        if (true === $this->saveCookie) {
            $this->saveCookies($this->parseCookies($response));
        }

        return json_encode(strval($response->getBody()));
    }

    /**
     * @param array $cookies
     * @return mixed
     */
    public function getDollarRate(array $cookies = []): float
    {
        if (0 === count($cookies)) {
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

        $html = (string)$response->getBody();

        $dom = HtmlDomParser::str_get_html($html);

        $dollarRate = $dom->find('#top_right_nav li .navbar-text .siteprof_currency', 0)->text();

        $matches = [];
        preg_match('/^1\$ = (.+?) грн /', $dollarRate, $matches);

        return (float)$matches[1];
    }
}
