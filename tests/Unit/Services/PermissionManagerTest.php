<?php

class PermissionManagerTest extends TestCase
{
    /** @var \Lockd\Services\PermissionManager */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new \Lockd\Services\PermissionManager();
    }

    public function tearDown()
    {
        unset($this->service);
        parent::tearDown();
    }

    public function testGrantGroupAccessToFolder()
    {
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->assertCount(0, $this->ee['group']->folders()->get());

        $this->assertTrue(
            $this->service->grantGroupAccessToFolder(
                $this->ee['group'],
                $this->ee['folder']
            )
        );

        $this->assertCount(1, $this->ee['group']->folders()->get());
    }

    public function testGrantGroupAccessToFolderAlreadyHasAccess()
    {
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->ee['group']->folders()->attach($this->ee['folder']);

        $this->assertCount(1, $this->ee['group']->folders()->get());

        $this->assertTrue(
            $this->service->grantGroupAccessToFolder(
                $this->ee['group'],
                $this->ee['folder']
            )
        );

        $this->assertCount(1, $this->ee['group']->folders()->get());
    }

    public function testRemoveGroupAccessFromFolder()
    {
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->ee['group']->folders()->attach($this->ee['folder']);

        $this->assertCount(1, $this->ee['group']->folders()->get());

        $this->assertTrue(
            $this->service->removeGroupAccessFromFolder(
                $this->ee['group'],
                $this->ee['folder']
            )
        );

        $this->assertCount(0, $this->ee['group']->folders()->get());
    }

    public function testRemoveGroupAccessFromFolderAlreadyHasNoAccess()
    {
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->assertCount(0, $this->ee['group']->folders()->get());

        $this->assertTrue(
            $this->service->removeGroupAccessFromFolder(
                $this->ee['group'],
                $this->ee['folder']
            )
        );

        $this->assertCount(0, $this->ee['group']->folders()->get());
    }
}