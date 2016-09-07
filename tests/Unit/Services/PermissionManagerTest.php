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

    public function testCheckUserIsInGroupTrue()
    {
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this->ee['group']->users()->attach($this->ee['user']);

        $this->assertTrue($this->service->checkUserIsInGroup($this->ee['user'], $this->ee['group']));
    }

    public function testCheckUserIsInGroupFalse()
    {
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this->assertFalse($this->service->checkUserIsInGroup($this->ee['user'], $this->ee['group']));
    }

    public function testCheckUserHasAccessToPassword()
    {
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();

        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create(['folder_id' => $this->ee['folder']->id]);

        $this->ee['group']->users()->attach($this->ee['user']);
        $this->ee['group']->folders()->attach($this->ee['folder']);

        $this->assertTrue(
            $this->service->checkUserHasAccessToPassword(
                $this->ee['user'],
                $this->ee['password']
            )
        );
    }

    public function testCheckUserHasAccessToPasswordNoAccess()
    {
        $this->ee['group1'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['group2'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();

        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create(['folder_id' => $this->ee['folder']->id]);

        $this->ee['group1']->users()->attach($this->ee['user']);
        $this->ee['group2']->folders()->attach($this->ee['folder']);

        $this->assertFalse(
            $this->service->checkUserHasAccessToPassword(
                $this->ee['user'],
                $this->ee['password']
            )
        );
    }
    
    public function testCheckUserHasAccessToFolder()
    {
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();

        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->ee['group']->users()->attach($this->ee['user']);
        $this->ee['group']->folders()->attach($this->ee['folder']);

        $this->assertTrue(
            $this->service->checkUserHasAccessToFolder(
                $this->ee['user'],
                $this->ee['folder']
            )
        );
    }

    public function testCheckUserHasAccessToFolderNoAccess()
    {
        $this->ee['group1'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['group2'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();

        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->ee['group1']->users()->attach($this->ee['user']);
        $this->ee['group2']->folders()->attach($this->ee['folder']);

        $this->assertFalse(
            $this->service->checkUserHasAccessToFolder(
                $this->ee['user'],
                $this->ee['folder']
            )
        );
    }
}