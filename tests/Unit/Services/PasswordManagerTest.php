<?php

class PasswordManagerTest extends TestCase
{
    /** @var \Lockd\Services\PasswordManager */
    private $service;

    private $encryptedString = 'encryptedstring';

    private $decryptedString = 'decryptedstring';

    public function setUp()
    {
        parent::setUp();

        $mockEncrypter = Mockery::mock(\Illuminate\Encryption\Encrypter::class);
        $mockEncrypter
            ->shouldReceive('encrypt')
            ->with('letmein')
            ->atMost(1)
            ->andReturn($this->encryptedString);

        $mockEncrypter
            ->shouldReceive('decrypt')
            ->with('letmein')
            ->atMost(1)
            ->andReturn($this->decryptedString);

        $this->service = new \Lockd\Services\PasswordManager($mockEncrypter);

        $this->ee['rootFolder'] = factory(\Lockd\Models\Folder::class)->create([
            'name' => 'Root',
            'parent_id' => 0,
        ]);
    }

    public function tearDown()
    {
        unset($this->service);
        parent::tearDown();
    }

    public function testCreate()
    {
        $data = [
            'folder_id' => $this->ee['rootFolder']->id,
            'name' => 'Test Password',
            'url' => 'http://localhost',
            'user' => 'test',
            'password' => 'letmein',
        ];

        $this->ee['password'] = $this->service->create(
            $this->ee['rootFolder'],
            $data['name'],
            $data['password'],
            $data['url'],
            $data['user']
        );

        $this->assertInstanceOf(\Lockd\Models\Password::class, $this->ee['password']);

        $data['password'] = $this->encryptedString;

        $this->seeInDatabase('da_password', $data);
    }

    public function testCreateNoData()
    {
        $result = $this->service->create(
            $this->ee['rootFolder'],
            '',
            ''
        );

        $this->assertFalse($result);

        $this->assertEquals('bad_request', $this->service->getError());
        $this->assertEquals(400, $this->service->getErrorCode());
        $this->assertEquals(['Please provide a name', 'Please provide a password'], $this->service->getErrorDescription());
    }

    public function testUpdate()
    {
        $data = [
            'folder' => $this->ee['rootFolder'],
            'name' => 'Test',
            'url' => 'http://testupdate',
            'user' => 'username',
            'password' => 'letmein',
        ];

        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create();

        $result = $this->service->update($this->ee['password'], $data);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $result);

        unset($data['folder']);
        $data['password'] = $this->encryptedString;
        $data['folder_id'] = $this->ee['rootFolder']->id;

        $this->seeInDatabase('da_password', $data);
    }

    public function testUpdateUntouched()
    {
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create();

        $data = [
            'name' => $this->ee['password']->name,
            'url' => $this->ee['password']->url,
            'user' => $this->ee['password']->user,
            'password' => $this->ee['password']->password,
        ];

        $this->seeInDatabase('da_password', $data);

        $result = $this->service->update($this->ee['password'], []);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $result);

        $this->seeInDatabase('da_password', $data);
    }

    public function testGetPassword()
    {
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create();

        $result = $this->service->getPassword($this->ee['password']);

        $this->assertEquals($this->decryptedString, $result);
    }
}