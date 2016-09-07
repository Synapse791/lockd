<?php

class GroupManagerTest extends TestCase
{
    /** @var \Lockd\Services\GroupManager */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new \Lockd\Services\GroupManager();
    }

    public function tearDown()
    {
        unset($this->service);
        parent::tearDown();
    }

    public function testCreate()
    {
        $this->ee['group'] = $this->service->create('Finance');

        $this->assertInstanceOf(\Lockd\Models\Group::class, $this->ee['group']);
        $this->seeInDatabase('au_group', [
            'name' => 'Finance',
        ]);
    }

    public function testCreateEmptyData()
    {
        $result = $this->service->create('');

        $this->assertFalse($result);
        $this->dontSeeInDatabase('au_group', ['name' => '']);

        $this->assertEquals('bad_request', $this->service->getError());
        $this->assertEquals(400, $this->service->getErrorCode());
        $this->assertEquals('Please provide a name', $this->service->getErrorDescription());
    }

    public function testCreateDuplicate()
    {
        $this->ee['group1'] = factory(\Lockd\Models\Group::class)->create();

        $this->assertFalse($this->service->create($this->ee['group1']->name));

        $this->assertEquals('conflict', $this->service->getError());
        $this->assertEquals(409, $this->service->getErrorCode());
        $this->assertEquals('A group already exists with that name!', $this->service->getErrorDescription());
    }

    public function testUpdate()
    {
        $this->ee['group1'] = factory(\Lockd\Models\Group::class)->create();

        $this->seeInDatabase('au_group', [
            'name' => $this->ee['group1']->name,
        ]);

        $result = $this->service->update($this->ee['group1'], ['name' => 'Finance']);

        $this->assertInstanceOf(\Lockd\Models\Group::class, $result);

        $this->seeInDatabase('au_group', [
            'name' => 'Finance',
        ]);
    }

    public function testUpdateUntouched()
    {
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this->seeInDatabase('au_group', [
            'name' => $this->ee['group']->name,
        ]);

        $result = $this->service->update($this->ee['group'], '');

        $this->assertInstanceOf(\Lockd\Models\Group::class, $result);

        $this->seeInDatabase('au_group', [
            'name' => $this->ee['group']->name,
        ]);
    }

    public function testAddUserToGroup()
    {
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this->dontSeeInDatabase('au_user_groups', [
            'user_id' => $this->ee['user']->id,
            'group_id' => $this->ee['group']->id,
        ]);

        $this->assertTrue(
            $this->service->addUserToGroup($this->ee['user'], $this->ee['group'])
        );

        $this->seeInDatabase('au_user_groups', [
            'user_id' => $this->ee['user']->id,
            'group_id' => $this->ee['group']->id,
        ]);
    }

    public function testRemoveUserFromGroup()
    {
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this->ee['group']->users()->attach($this->ee['user']);

        $this->seeInDatabase('au_user_groups', [
            'user_id' => $this->ee['user']->id,
            'group_id' => $this->ee['group']->id,
        ]);

        $this->assertTrue(
            $this->service->removeUserFromGroup($this->ee['user'], $this->ee['group'])
        );

        $this->dontSeeInDatabase('au_user_groups', [
            'user_id' => $this->ee['user']->id,
            'group_id' => $this->ee['group']->id,
        ]);
    }
}