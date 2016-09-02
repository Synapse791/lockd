<?php

class UserManagerTest extends TestCase
{
    /** @var \Lockd\Services\UserManager */
    private $service;

    /** @var array */
    private $data;
    
    public function setUp()
    {
        parent::setUp();
        $mockHasher = Mockery::mock(\Illuminate\Hashing\BcryptHasher::class);
        $mockHasher
            ->shouldReceive('make')
            ->with('letmein')
            ->atMost(1)
            ->andReturn('letmein');

        $this->service = new \Lockd\Services\UserManager($mockHasher);
        
        $this->data = [ 
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test@user.com',
            'password' => 'letmein',
        ];
    }

    public function tearDown()
    {
        if (count($this->ee) > 0)
            foreach ($this->ee as $entity)
                $entity->delete();

        unset($this->service);
        unset($this->data);
        parent::tearDown();
    }

    public function testCreate()
    {
        $this->ee['user'] = $this->service->create(
            $this->data['firstName'],
            $this->data['lastName'],
            $this->data['email'],
            $this->data['password']
        );

        $this->assertInstanceOf(\Lockd\Models\User::class, $this->ee['user']);
        $this->seeInDatabase('au_user', $this->data);
    }

    public function testCreateEmptyData()
    {
        $this->data = [
            'firstName' => '',
            'lastName' => '',
            'email' => '',
            'password' => '',
        ];

        $result = $this->service->create(
            $this->data['firstName'],
            $this->data['lastName'],
            $this->data['email'],
            $this->data['password']
        );

        $this->assertFalse($result);
        $this->dontSeeInDatabase('au_user', $this->data);

        $this->assertEquals('bad_request', $this->service->getError());
        $this->assertEquals(400, $this->service->getErrorCode());
        $this->assertEquals([
            'Please provide a first name',
            'Please provide a last name',
            'Please provide an email',
            'Please provide a password of at least 6 characters',
        ], $this->service->getErrorDescription());
    }

    public function testCreateShortPassword()
    {
        $this->data['password'] = 'open';

        $result = $this->service->create(
            $this->data['firstName'],
            $this->data['lastName'],
            $this->data['email'],
            $this->data['password']
        );

        $this->assertFalse($result);
        $this->dontSeeInDatabase('au_user', $this->data);

        $this->assertEquals('bad_request', $this->service->getError());
        $this->assertEquals(400, $this->service->getErrorCode());
        $this->assertEquals(['Please provide a password of at least 6 characters'], $this->service->getErrorDescription());
    }

    public function testCreateDuplicate()
    {
        $this->ee['user1'] = factory(\Lockd\Models\User::class)->create();

        $this->assertFalse($this->service->create(
            $this->data['firstName'],
            $this->data['lastName'],
            $this->ee['user1']->email,
            $this->data['password']
        ));

        $this->assertEquals('conflict', $this->service->getError());
        $this->assertEquals(409, $this->service->getErrorCode());
        $this->assertEquals('A user already exists with that email!', $this->service->getErrorDescription());
    }
}