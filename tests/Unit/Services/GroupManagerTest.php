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
        if (count($this->ee) > 0)
            foreach ($this->ee as $entity)
                $entity->delete();

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
}