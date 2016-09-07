<?php

class PasswordControllerTest extends FunctionalTestCase
{
    private $authGroup;

    public function setUp()
    {
        parent::setUp();
        $this->authUser = factory(\Lockd\Models\User::class)->create();
        $this->authGroup = factory(\Lockd\Models\Group::class)->create();
        $this->authGroup->users()->attach($this->authUser);

        $this->be($this->authUser);
    }

    public function testGetFromFolder()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create(['folder_id' => $this->ee['folder']->id]);
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create(['folder_id' => $this->ee['folder']->id]);
        $this->ee['password3'] = factory(\Lockd\Models\Password::class)->create(['folder_id' => $this->ee['folder']->id]);

        $this->authGroup->folders()->attach($this->ee['folder']);

        $this
            ->get("/api/folder/{$this->ee['folder']->id}/passwords")
            ->seeStatusCode(200)
            ->seeJson([
                'id' => $this->ee['password1']->id,
                'name' => $this->ee['password1']->name,
                'url' => $this->ee['password1']->url,
                'user' => $this->ee['password1']->user,
                'password' => base64_encode('letmein'),
            ])
            ->seeJson([
                'id' => $this->ee['password2']->id,
                'name' => $this->ee['password2']->name,
                'url' => $this->ee['password2']->url,
                'user' => $this->ee['password2']->user,
                'password' => base64_encode('letmein'),
            ])
            ->seeJson([
                'id' => $this->ee['password3']->id,
                'name' => $this->ee['password3']->name,
                'url' => $this->ee['password3']->url,
                'user' => $this->ee['password3']->user,
                'password' => base64_encode('letmein'),
            ]);
    }

    public function testCreate()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->authGroup->folders()->attach($this->ee['folder']);

        $this->assertCount(0, $this->ee['folder']->passwords()->get());

        $data = [
            'name' => 'Test Password',
            'url' => 'http://test',
            'user' => 'UserName',
            'password' => 'letmein',
            'password_confirmation' => 'letmein',
        ];

        $this
            ->put("/api/folder/{$this->ee['folder']->id}/passwords", $data)
            ->seeStatusCode(201)
            ->seeJson([
                'data' => 'Password created successfully',
            ]);

        unset($data['password']);
        unset($data['password_confirmation']);
        $data['folder_id'] = $this->ee['folder']->id;

        $this->seeInDatabase('da_password', $data);

        $this->assertCount(1, $this->ee['folder']->passwords()->get());

        $this->ee['password'] = \Lockd\Models\Password::where([
            ['name', $data['name']],
            ['url', $data['url']],
            ['user', $data['user']],
            ['folder_id', $data['folder_id']],
        ])->first();
    }

    public function testCreateBadRequest()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();

        $this->authGroup->folders()->attach($this->ee['folder']);

        $this
            ->put("/api/folder/{$this->ee['folder']->id}/passwords", [])
            ->seeStatusCode(400)
            ->seeJson([
                'error' => 'bad_request',
                'errorDescription' => [
                    'The name field is required.',
                    'The password field is required.'
                ],
            ]);
    }

    public function testCreateNotFound()
    {
        $this
            ->put("/api/folder/1000/passwords", [])
            ->seeStatusCode(404)
            ->seeJson([
                'error' => 'not_found',
                'errorDescription' => 'Folder with ID 1000 not found',
            ]);
    }

    public function testCreateConflict()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create([
            'name' => 'TestPassword',
            'folder_id' => $this->ee['folder']->id,
        ]);

        $this->authGroup->folders()->attach($this->ee['folder']);

        $this
            ->put("/api/folder/{$this->ee['folder']->id}/passwords", [
                'name' => 'TestPassword',
                'password' => 'letmein',
                'password_confirmation' => 'letmein',
            ])
            ->seeStatusCode(409)
            ->seeJson([
                'error' => 'conflict',
                'errorDescription' => 'A password with that name already exists in this folder',
            ]);
    }

    public function testUpdate()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder1']->id,
        ]);

        $this->authGroup->folders()->attach($this->ee['folder1']);

        $this->seeInDatabase('da_password', [
            'name' => $this->ee['password']->name,
            'url' => $this->ee['password']->url,
            'user' => $this->ee['password']->user,
            'folder_id' => $this->ee['folder1']->id,
        ]);

        $this
            ->patch("/api/folder/{$this->ee['folder1']->id}/passwords/{$this->ee['password']->id}", [
                'name' => 'UpdateTest',
                'url' => 'http://updatetest',
                'user' => 'update_user',
                'folder_id' => $this->ee['folder2']->id,
            ])
            ->seeStatusCode(200)
            ->seeJson([
                'data' => 'Password updated successfully',
            ]);

        $this->seeInDatabase('da_password', [
            'name' => 'UpdateTest',
            'url' => 'http://updatetest',
            'user' => 'update_user',
            'folder_id' => $this->ee['folder2']->id,
        ]);
    }

    public function testUpdateDuplicateInSameFolder()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder']->id,
        ]);
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create([
            'folder_id' => $this->ee['folder']->id,
        ]);

        $this->authGroup->folders()->attach($this->ee['folder']);

        $this
            ->patch("/api/folder/{$this->ee['folder']->id}/passwords/{$this->ee['password2']->id}", [
                'name' => $this->ee['password1']->name,
            ])
            ->seeStatusCode(409)
            ->seeJson([
                'error' => 'conflict',
                'errorDescription' => 'A password with that name already exists in that folder',
            ]);
    }

    public function testUpdateDuplicateInNewFolder()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create([
            'name' => 'Password 1',
            'folder_id' => $this->ee['folder1']->id,
        ]);
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create([
            'name' => 'Password 2',
            'folder_id' => $this->ee['folder2']->id,
        ]);

        $this->authGroup->folders()->attach($this->ee['folder1']);

        $this
            ->patch("/api/folder/{$this->ee['folder1']->id}/passwords/{$this->ee['password1']->id}", [
                'name' => $this->ee['password2']->name,
                'folder_id' => $this->ee['password2']->folder->id,
            ])
            ->seeStatusCode(409)
            ->seeJson([
                'error' => 'conflict',
                'errorDescription' => 'A password with that name already exists in that folder',
            ]);
    }

    public function testUpdateFolderNotFound()
    {
        $this
            ->patch("/api/folder/1000/passwords/1000", [])
            ->seeStatusCode(404)
            ->seeJson([
                'error' => 'not_found',
                'errorDescription' => 'Folder with ID 1000 not found',
            ]);
    }

    public function testUpdatePasswordNotFound()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();

        $this->authGroup->folders()->attach($this->ee['folder1']);

        $this
            ->patch("/api/folder/{$this->ee['folder1']->id}/passwords/1000", [
                'name' => 'UpdateTest',
                'url' => 'http://updatetest',
                'user' => 'update_user',
                'folder_id' => 0,
            ])
            ->seeStatusCode(404)
            ->seeJson([
                'error' => 'not_found',
                'errorDescription' => 'Password with ID 1000 not found',
            ]);
    }
}