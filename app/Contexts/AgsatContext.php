<?php

namespace App\Contexts;

class AgsatContext
{
    /** @var string $login */
    protected $login;

    /** @var string $password */
    protected $password;

    /** @var string $userAgent */
    protected $userAgent;

    /**
     * AgsatContext constructor.
     * @param string $login
     * @param string $password
     * @param string $userAgent
     */
    public function __construct(string $login, string $password, string $userAgent)
    {
        $this->login = $login;
        $this->password = $password;
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return AgsatContext
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return AgsatContext
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return AgsatContext
     */
    public function setUserAgent(string $userAgent): AgsatContext
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
