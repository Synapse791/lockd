<?php

class PasswordHydratorTest extends TestCase
{
    /** @var \Lockd\Hydrators\PasswordHydrator */
    private $hydrator;

    public function setUp()
    {
        parent::setUp();

        $mockEncrypter = Mockery::mock(\Illuminate\Encryption\Encrypter::class);
        $mockEncrypter
            ->shouldReceive('decrypt')
            ->andReturn('letmein');

        $this->hydrator = new \Lockd\Hydrators\PasswordHydrator($mockEncrypter);
    }

    public function testHydrate()
    {
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create();

        $result = $this->hydrator->hydrate($this->ee['password']);

        $this->assertEquals(
            [
                'id' => $this->ee['password']->id,
                'name' => $this->ee['password']->name,
                'url' => $this->ee['password']->url,
                'user' => $this->ee['password']->user,
                'password' => base64_encode('letmein'),
            ], $result);
    }
}