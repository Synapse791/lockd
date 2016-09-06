<?php

class FolderManagerTest extends TestCase
{
    /** @var \Lockd\Services\FolderManager */
    private $service;

    private function setRootFolder()
    {
        $this->ee['root'] = factory(\Lockd\Models\Folder::class)->create([
            'name' => 'Root',
            'parent_id' => 0,
        ]);
    }

    public function setUp()
    {
        parent::setUp();
        $this->service = new \Lockd\Services\FolderManager();
    }

    public function tearDown()
    {
        unset($this->service);
        parent::tearDown();
    }

    public function testCreate()
    {
        $this->setRootFolder();

        $this->ee['folder'] = $this->service->create($this->ee['root'], 'Finance');

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $this->ee['folder']);
        $this->seeInDatabase('da_folder', [
            'name' => 'Finance',
            'parent_id' => $this->ee['root']->id,
        ]);
    }

    public function testCreateEmptyData()
    {
        $this->setRootFolder();

        $result = $this->service->create($this->ee['root'], '');

        $this->assertFalse($result);
        $this->dontSeeInDatabase('da_folder', ['name' => '']);

        $this->assertEquals('bad_request', $this->service->getError());
        $this->assertEquals(400, $this->service->getErrorCode());
        $this->assertEquals('Please provide a name', $this->service->getErrorDescription());
    }

    public function testUpdate()
    {
        $this->setRootFolder();

        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();

        $this->seeInDatabase('da_folder', [
            'parent_id' => 0,
            'name' => $this->ee['folder1']->name,
        ]);

        $result = $this->service->update($this->ee['folder1'], [
            'name' => 'Finance',
            'parentFolder' => $this->ee['root'],
        ]);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $result);

        $this->seeInDatabase('da_folder', [
            'name' => 'Finance',
            'parent_id' => $this->ee['root']->id,
        ]);
    }

    public function testUpdateUntouched()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->seeInDatabase('da_folder', [
            'name' => $this->ee['folder']->name,
        ]);

        $result = $this->service->update($this->ee['folder'], []);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $result);

        $this->seeInDatabase('da_folder', [
            'name' => $this->ee['folder']->name,
        ]);
    }
}