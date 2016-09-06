<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * Extra Entities created during tests
     *
     * @var array
     */
    protected $ee;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    public function tearDown()
    {
        if (count($this->ee) > 0)
            foreach ($this->ee as $entity)
                if (is_object($entity))
                    $entity->delete();
                else
                    unset($entity);

        parent::tearDown();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
