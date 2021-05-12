<?php

class ExampleTest extends TestCase
{
    /**
     * @return void
     */
    public function testExample(): void
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(),
            $this->response->getContent()
        );
    }
}