<?php

class FolderHydratorTest extends TestCase
{
    /** @var \Lockd\Hydrators\FolderHydrator */
    private $hydrator;

    public function setUp()
    {
        parent::setUp();

        $mockFolderRepository = Mockery::mock(\Lockd\Repositories\DefaultFolderRepository::class);
        $mockFolderRepository
            ->shouldReceive('countSubFolders')
            ->andReturn(3);

        $mockPasswordRepository = Mockery::mock(\Lockd\Repositories\DefaultPasswordRepository::class);
        $mockPasswordRepository
            ->shouldReceive('countPasswordsInFolder')
            ->andReturn(2);

        $this->hydrator = new \Lockd\Hydrators\FolderHydrator($mockFolderRepository, $mockPasswordRepository);
    }

    public function testHydrate()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create([
            'parent_id' => $this->ee['folder1']->id,
        ]);

        $result = $this->hydrator->hydrate($this->ee['folder2']);

        $this->assertEquals(
            [
                'id' => $this->ee['folder2']->id,
                'name' => $this->ee['folder2']->name,
                'parent_id' => $this->ee['folder1']->id,
                'folder_count' => 3,
                'password_count' => 2,
            ], $result);
    }
}