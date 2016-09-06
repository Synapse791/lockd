<?php

class DefaultPasswordRepositoryTest extends TestCase
{
    /** @var \Lockd\Repositories\DefaultPasswordRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new \Lockd\Repositories\DefaultPasswordRepository();
    }

    public function tearDown()
    {
        unset($this->repository);
        parent::tearDown();
    }

    public function testFind()
    {
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create();
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create();
        $this->ee['password3'] = factory(\Lockd\Models\Password::class)->create();

        $results = $this->repository->find();

        $this->assertCount(3, $results);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $results[0]);
        $this->assertEquals($this->ee['password1']->id, $results[0]->id);
        $this->assertEquals($this->ee['password1']->name, $results[0]->name);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $results[1]);
        $this->assertEquals($this->ee['password2']->id, $results[1]->id);
        $this->assertEquals($this->ee['password2']->name, $results[1]->name);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $results[2]);
        $this->assertEquals($this->ee['password3']->id, $results[2]->id);
        $this->assertEquals($this->ee['password3']->name, $results[2]->name);
    }

    public function testFindWithParameters()
    {
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create();
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create();
        $this->ee['password3'] = factory(\Lockd\Models\Password::class)->create();

        $results = $this->repository->find([
            'name' => $this->ee['password2']->name,
        ]);

        $this->assertCount(1, $results);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $results[0]);
        $this->assertEquals($this->ee['password2']->id, $results[0]->id);
        $this->assertEquals($this->ee['password2']->name, $results[0]->name);
    }

    public function testFindOneById()
    {
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create();
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create();
        $this->ee['password3'] = factory(\Lockd\Models\Password::class)->create();

        $result = $this->repository->findOneById($this->ee['password2']->id);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $result);
        $this->assertEquals($this->ee['password2']->id, $result->id);
        $this->assertEquals($this->ee['password2']->name, $result->name);
    }

    public function testFindOneByIdNotFound()
    {
        $this->assertNull($this->repository->findOneById(1000));
    }

    public function testFindPasswordsInFolder()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create();

        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder']->id
        ]);

        $this->ee['password3'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder']->id
        ]);

        $results = $this->repository->findPasswordsInFolder($this->ee['folder']);

        $this->assertCount(2, $results);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $results[0]);
        $this->assertEquals($this->ee['password2']->id, $results[0]->id);
        $this->assertEquals($this->ee['password2']->name, $results[0]->name);

        $this->assertInstanceOf(\Lockd\Models\Password::class, $results[1]);
        $this->assertEquals($this->ee['password3']->id, $results[1]->id);
        $this->assertEquals($this->ee['password3']->name, $results[1]->name);
    }

    public function testFindPasswordsInFolderNoPasswords()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $results = $this->repository->findPasswordsInFolder($this->ee['folder']);

        $this->assertCount(0, $results);
    }

    public function testFindFolder()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder']->id,
        ]);

        $result = $this->repository->findFolder($this->ee['password']);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $result);
        $this->assertEquals($this->ee['folder']->id, $result->id);
        $this->assertEquals($this->ee['folder']->name, $result->name);
    }

    public function testCount()
    {
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create();
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create();
        $this->ee['password3'] = factory(\Lockd\Models\Password::class)->create();

        $result = $this->repository->count();

        $this->assertEquals(3, $result);
    }

    public function testCountNoResults()
    {
        $result = $this->repository->count();

        $this->assertEquals(0, $result);
    }

    public function testCountSubPasswords()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder']->id
        ]);
        $this->ee['password3'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder']->id
        ]);

        $result = $this->repository->countPasswordsInFolder($this->ee['folder']);

        $this->assertEquals(2, $result);
    }

    public function testCountSubPasswordsNoPasswords()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $result = $this->repository->countPasswordsInFolder($this->ee['folder']);

        $this->assertEquals(0, $result);
    }
}