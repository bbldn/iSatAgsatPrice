<?php

namespace App\Services;

use App\Contexts\AgsatContext;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Kozz\Laravel\Facades\Guzzle;
use KubAT\PhpSimple\HtmlDomParser;

class Agsat
{
    /** @var AgsatContext $agsatContext */
    protected $agsatContext;

    /** @var string $cookieKey */
    protected $cookieKey = 'PHPSESSID';

    /** @var bool $saveCookie */
    protected $saveCookie = false;

    /**
     * Agsat constructor.
     * @param AgsatContext $agsatContext
     */
    public function __construct(AgsatContext $agsatContext)
    {
        $this->agsatContext = $agsatContext;
    }

    /**
     * @param bool $saveCookie
     * @return array
     */
    protected function login(bool $saveCookie = true): array
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Response $response */
        $response = Guzzle::request('POST', 'https://www.agsat.com.ua/login/', [
            'form_params' => [
                'login' => $this->agsatContext->getLogin(),
                'password' => $this->agsatContext->getPassword(),
            ],
            'headers' => [
                'User-Agent' => $this->agsatContext->getUserAgent(),

            ],
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

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Response $response */
        $response = Guzzle::request('GET', 'https://www.agsat.com.ua/json/pricelist/', [
            'headers' => [
                'User-Agent' => $this->agsatContext->getUserAgent(),
            ],
            'cookies' => $cookies,
            'allow_redirects' => false,
        ]);

        if (true === $this->saveCookie) {
            $this->saveCookies($this->parseCookies($response));
        }

        return json_encode((string)$response->getBody());
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

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Response $response */
        $response = Guzzle::request('GET', 'https://www.agsat.com.ua', [
            'headers' => [
                'User-Agent' => $this->agsatContext->getUserAgent(),
            ],
            'cookies' => $cookies,
            'allow_redirects' => false,
        ]);

        $dom = HtmlDomParser::str_get_html((string)$response->getBody());
        $dollarRate = $dom->find('#top_right_nav li .navbar-text .siteprof_currency', 0)->text();

        preg_match('/^1\$ = (.+?) грн /', $dollarRate, $matches);

        return (float)$matches[1];
    }
}
