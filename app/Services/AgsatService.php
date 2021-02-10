<?php

namespace App\Services;

use App\Contexts\AgsatContext;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class AgsatService
{
    /** @var AgsatContext */
    private $agsatContext;

    /**
     * AgsatService constructor.
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

        $crawler = new Crawler($response->body());
        $crawler = $crawler->filter('#top_right_nav li .navbar-text .siteprof_currency')->first();
        if (0 === $crawler->count()) {
            return 0.0;
        }

        $result = preg_match('/^1\$ = (.+?) грн$/', $crawler->text(), $matches);
        if (0 === $result) {
            return 0.0;
        }

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
