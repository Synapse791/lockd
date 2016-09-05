<?php

class GroupControllerTest extends FunctionalTestCase
{
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

    public function testCreate()
    {
        $this
            ->put('/api/group', ['name' => 'Finance'])
            ->assertResponseStatus(201)
            ->seeJson([
                'data' => 'Group created successfully',
            ]);

        $this->seeInDatabase('au_group', ['name' => 'Finance']);

        $this->ee['group'] = \Lockd\Models\Group::where('name', 'Finance')->first();
    }

    public function testCreateBadRequest()
    {
        $this
            ->put('/api/group', [])
            ->assertResponseStatus(400)
            ->seeJson([
                'error' => 'bad_request',
                'errorDescription' => ["The name field is required."],
            ]);

        $this->dontSeeInDatabase('au_group', ['name' => 'Finance']);
    }

    public function testCreateConflict()
    {
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this
            ->put('/api/group', ['name' => $this->ee['group']->name])
            ->assertResponseStatus(409)
            ->seeJson([
                'error' => 'conflict',
                'errorDescription' => 'A group already exists with that name!',
            ]);
    }
}