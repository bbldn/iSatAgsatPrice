<?php

namespace App\Services;

use App\Contexts\AgsatContext;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use KubAT\PhpSimple\HtmlDomParser;

class Agsat extends Service
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
     * @return string
     */
    public function getAll(array $cookies = []): string
    {
        if (0 === count($cookies)) {
            $cookies = $this->login();
        }

        $response = Http::withCookies($cookies, 'www.agsat.com.ua')
            ->withOptions(['allow_redirects' => false])
            ->withHeaders(['User-Agent' => $this->agsatContext->getUserAgent()])
            ->get('https://www.agsat.com.ua/json/pricelist/');

        $json = json_encode($response->body());
        if (false === $json) {
            return '[]';
        }

        return $json;
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

        $response = Http::withCookies($cookies, 'www.agsat.com.ua')
            ->withOptions(['allow_redirects' => false])
            ->withHeaders(['User-Agent' => $this->agsatContext->getUserAgent()])
            ->get('https://www.agsat.com.ua');

        $dom = HtmlDomParser::str_get_html($response->body());
        $dollarRate = $dom->find('#top_right_nav li .navbar-text .siteprof_currency', 0)->text();

        preg_match('/^1\$ = (.+?) грн /', $dollarRate, $matches);

        return (float)$matches[1];
    }

    /**
     * @return array
     */
    protected function login(): array
    {
        $response = Http::withOptions(['allow_redirects' => false])
            ->withHeaders(['User-Agent' => $this->agsatContext->getUserAgent()])
            ->asForm()
            ->post('https://www.agsat.com.ua/login/', [
                'login' => $this->agsatContext->getLogin(),
                'password' => $this->agsatContext->getPassword(),
            ]);

        return $this->parseCookies($response);
    }

    /**
     * @param Response $response
     * @return array
     */
    protected function parseCookies(Response $response): array
    {
        $cookies = [];
        $headers = $response->headers()['Set-Cookie'];

        foreach ($headers as $value) {
            if (1 === preg_match('/^([^=]+)=([^;]+);/', $value, $matches)) {
                $cookies[$matches[1]] = $matches[2];
            }
        }

        return $cookies;
    }
}
