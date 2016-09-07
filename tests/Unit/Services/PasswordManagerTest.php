<?php

class PasswordManagerTest extends TestCase
{
    /** @var \Lockd\Services\PasswordManager */
    private $service;

    private $encryptedString = 'encryptedstring';

    private $decryptedString = 'decryptedstring';

    private function setService($repositoryFindReturn = [])
    {
        $mockEncrypter = Mockery::mock(\Illuminate\Encryption\Encrypter::class);
        $mockEncrypter
            ->shouldReceive('encrypt')
            ->atMost(1)
            ->andReturn($this->encryptedString);

        $mockEncrypter
            ->shouldReceive('decrypt')
            ->atMost(1)
            ->andReturn($this->decryptedString);

        $mockRepository = Mockery::mock(\Lockd\Repositories\DefaultPasswordRepository::class);
        $mockRepository
            ->shouldReceive('find')
            ->atMost(1)
            ->andReturn($repositoryFindReturn);

        $this->service = new \Lockd\Services\PasswordManager($mockEncrypter, $mockRepository);
    }

    public function setUp()
    {
        parent::setUp();

        $this->setService();

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

    public function testCreateConflict()
    {
        $this->setService(['result']);

        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create([
            'name' => 'TestPassword',
            'folder_id' => $this->ee['rootFolder']->id,
        ]);

        $this->assertFalse(
            $this->service->create(
                $this->ee['rootFolder'],
                'TestPassword',
                'letmein'
            )
        );

        $this->assertEquals('conflict', $this->service->getError());
        $this->assertEquals(409, $this->service->getErrorCode());
        $this->assertEquals('A password with that name already exists in this folder', $this->service->getErrorDescription());
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

        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder']->id,
        ]);

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

    public function testUpdateDuplicateSameFolder()
    {
        $this->setService(['result']);

        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create([
            'name' => 'Password 1',
            'folder_id' => $this->ee['folder']->id,
        ]);
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create([
            'name' => 'Password 2',
            'folder_id' => $this->ee['folder']->id,
        ]);

        $this->assertFalse(
            $this->service->update(
                $this->ee['password1'],
                ['name' => 'Password 2']
            )
        );

        $this->assertEquals('conflict', $this->service->getError());
        $this->assertEquals(409, $this->service->getErrorCode());
        $this->assertEquals('A password with that name already exists in that folder', $this->service->getErrorDescription());
    }

    public function testUpdateDuplicateNewFolder()
    {
        $this->setService(['result']);

        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create([
            'name' => 'Password 1',
            'folder_id' => $this->ee['folder1']->id,
        ]);
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create([
            'name' => 'Password 2',
            'folder_id' => $this->ee['folder2']->id,
        ]);

        $this->assertFalse(
            $this->service->update(
                $this->ee['password1'],
                [
                    'name' => 'Password 2',
                    'folder' => $this->ee['folder2']
                ]
            )
        );

        $this->assertEquals('conflict', $this->service->getError());
        $this->assertEquals(409, $this->service->getErrorCode());
        $this->assertEquals('A password with that name already exists in that folder', $this->service->getErrorDescription());
    }

    public function testGetPassword()
    {
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create();

        $result = $this->service->getPassword($this->ee['password']);

        $this->assertEquals($this->decryptedString, $result);
    }
}