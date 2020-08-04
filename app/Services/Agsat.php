<?php

namespace App\Services;

use App\Contexts\AgsatContext;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Psr7\Response;
use Kozz\Laravel\Facades\Guzzle;
use KubAT\PhpSimple\HtmlDomParser;

class Agsat
{
    /** @var AgsatContext $agsatContext */
    protected $agsatContext;

    /**
     * Agsat constructor.
     * @param AgsatContext $agsatContext
     */
    public function __construct(AgsatContext $agsatContext)
    {
        $this->agsatContext = $agsatContext;
    }

    /**
     * @param array $cookies
     * @return false|string
     */
    public function getAll(array $cookies = []): string
    {
        if (0 === count($cookies)) {
            $cookies = $this->login();
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

        return json_encode((string)$response->getBody());
    }

    /**
     * @param array $cookies
     * @return mixed
     */
    public function getHryvniaRate(array $cookies = []): float
    {
        if (0 === count($cookies)) {
            $cookies = $this->login();
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

    /**
     * @return CookieJar
     */
    protected function login(): CookieJar
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

        return SessionCookieJar::fromArray($this->parseCookies($response), 'www.agsat.com.ua');
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
}
