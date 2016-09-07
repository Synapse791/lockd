<?php

class FolderControllerTest extends FunctionalTestCase
{
    public function testGetSingleFolder()
    {
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();

        $this->ee['group']->users()->attach($this->ee['user']);
        $this->ee['group']->folders()->attach($this->ee['folder1']);
        $this->ee['group']->folders()->attach($this->ee['folder2']);

        $this->be($this->ee['user']);

        $this
            ->get("/api/folder/{$this->ee['folder2']->id}")
            ->assertResponseStatus(200)
            ->seeJson([
                'id' => $this->ee['folder2']->id,
                'name' => $this->ee['folder2']->name,
            ]);
    }

    public function testGetSubFolders()
    {
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create(['parent_id' => $this->ee['folder1']->id]);
        $this->ee['folder3'] = factory(\Lockd\Models\Folder::class)->create(['parent_id' => $this->ee['folder1']->id]);

        $this->ee['group']->users()->attach($this->ee['user']);
        $this->ee['group']->folders()->attach($this->ee['folder1']);
        $this->ee['group']->folders()->attach($this->ee['folder2']);
        $this->ee['group']->folders()->attach($this->ee['folder3']);

        $this->be($this->ee['user']);

        $this
            ->get("/api/folder/{$this->ee['folder1']->id}/folders")
            ->assertResponseStatus(200)
            ->seeJson([
                'id' => $this->ee['folder2']->id,
                'name' => $this->ee['folder2']->name,
            ])
            ->seeJson([
                'id' => $this->ee['folder3']->id,
                'name' => $this->ee['folder3']->name,
            ]);
    }

    public function testGetParent()
    {
        $this->ee['user'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['group'] = factory(\Lockd\Models\Group::class)->create();

        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create(['parent_id' => $this->ee['folder1']->id]);

        $this->ee['group']->users()->attach($this->ee['user']);
        $this->ee['group']->folders()->attach($this->ee['folder1']);
        $this->ee['group']->folders()->attach($this->ee['folder2']);

        $this->be($this->ee['user']);

        $this
            ->get("/api/folder/{$this->ee['folder2']->id}/parent")
            ->assertResponseStatus(200)
            ->seeJson([
                'id' => $this->ee['folder1']->id,
                'name' => $this->ee['folder1']->name,
            ]);
    }

    public function testGetSingleFolderNoAccess()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this
            ->get("/api/folder/{$this->ee['folder']->id}")
            ->seeStatusCode(401)
            ->seeJson([
                'error' => 'unauthorized',
                'errorDescription' => 'You do not have access to that folder',
            ]);
    }

    public function testGetNotFound()
    {
        $this
            ->get('/api/folder/1000/parent')
            ->assertResponseStatus(404)
            ->seeJson([
                'error' => 'not_found',
                'errorDescription' => 'Folder with ID 1000 not found',
            ]);
    }

    public function testGetUnknownOption()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this
            ->get("/api/folder/{$this->ee['folder']->id}/nothing")
            ->seeStatusCode(404);
    }

    public function testCreate()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();

        $data = [
            'name' => 'Test Folder',
            'parent_id' => $this->ee['folder1']->id,
        ];

        $this
            ->put('/api/folder', $data)
            ->seeStatusCode(201)
            ->seeJson([
                'data' => 'Folder created successfully',
            ]);

        $this->seeInDatabase('da_folder', $data);

        $this->ee['folder2'] = \Lockd\Models\Folder::where($data)->first();
        $this->assertInstanceOf(\Lockd\Models\Folder::class, $this->ee['folder2']);
    }

    public function testCreateBadRequest()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();

        $this
            ->put('/api/folder')
            ->seeStatusCode(400)
            ->seeJson([
                'error' => 'bad_request',
                'errorDescription' => ['The name field is required.', 'The parent id field is required.'],
            ]);
    }

    public function testCreateNotFound()
    {
        $this
            ->put('/api/folder', [
                'parent_id' => 1000,
                'name' => 'Test Folder',
            ])
            ->seeStatusCode(404)
            ->seeJson([
                'error' => 'not_found',
                'errorDescription' => 'Parent folder with ID 1000 not found',
            ]);
    }

    public function testUpdate()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();

        $this->seeInDatabase('da_folder', [
            'id' => $this->ee['folder2']->id,
            'name' => $this->ee['folder2']->name,
            'parent_id' => "0",
        ]);

        $this
            ->patch("/api/folder/{$this->ee['folder2']->id}", [
                'name' => 'Updated Folder',
                'parent_id' => $this->ee['folder1']->id,
            ])
            ->seeStatusCode(200)
            ->seeJson(['data' => 'Folder updated successfully']);

        $this->seeInDatabase('da_folder', [
            'id' => $this->ee['folder2']->id,
            'name' => 'Updated Folder',
            'parent_id' => $this->ee['folder1']->id,
        ]);
    }

    public function testUpdateBadRequest()
    {
        $this
            ->patch('/api/folder/1', [])
            ->seeStatusCode(400)
            ->seeJson([
                'error' => 'bad_request',
                'errorDescription' => 'Please provide at least one of the following fields: name, parent_id',
            ]);
    }

    public function testUpdateNotFound()
    {
        $this
            ->patch('/api/folder/1000', [
                'name' => 'Updated',
            ])
            ->seeStatusCode(404)
            ->seeJson([
                'error' => 'not_found',
                'errorDescription' => 'Folder with ID 1000 not found',
            ]);
    }

    public function testUpdateParentNotFound()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this
            ->patch("/api/folder/{$this->ee['folder']->id}", [
                'parent_id' => 1000,
            ])
            ->seeStatusCode(404)
            ->seeJson([
                'error' => 'not_found',
                'errorDescription' => 'Parent folder with ID 1000 not found',
            ]);
    }
}