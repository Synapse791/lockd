<?php

class GroupControllerTest extends TestCase
{
    public function tearDown()
    {
        if (count($this->ee) > 0)
            foreach ($this->ee as $entity)
                $entity->delete();

        parent::tearDown();
    }

    public function testGetAllGroups()
    {
        $this->ee['group1'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['group2'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['group3'] = factory(\Lockd\Models\Group::class)->create();

        $this
            ->get('/api/group')
            ->assertResponseStatus(200)
            ->seeJson([
                'data' => [
                    [
                        'id' => $this->ee['group1']->id,
                        'name' => $this->ee['group1']->name,
                    ],
                    [
                        'id' => $this->ee['group2']->id,
                        'name' => $this->ee['group2']->name,
                    ],
                    [
                        'id' => $this->ee['group3']->id,
                        'name' => $this->ee['group3']->name,
                    ]
                ]
            ]);
    }

    public function testGetGroupById()
    {
        $this->ee['group1'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['group2'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['group3'] = factory(\Lockd\Models\Group::class)->create();

        $this
            ->get('/api/group?id=' . $this->ee['group2']->id)
            ->assertResponseStatus(200)
            ->seeJson([
                'data' => [
                    'id' => $this->ee['group2']->id,
                    'name' => $this->ee['group2']->name,
                ]
            ]);
    }

    public function testGetGroupByName()
    {
        $this->ee['group1'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['group2'] = factory(\Lockd\Models\Group::class)->create();
        $this->ee['group3'] = factory(\Lockd\Models\Group::class)->create();

        $this
            ->get('/api/group?name=' . $this->ee['group2']->name)
            ->assertResponseStatus(200)
            ->seeJson([
                'data' => [
                    'id' => $this->ee['group2']->id,
                    'name' => $this->ee['group2']->name,
                ]
            ]);
    }
}