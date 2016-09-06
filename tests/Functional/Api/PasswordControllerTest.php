<?php

class PasswordControllerTest extends FunctionalTestCase
{
    public function testGetFromFolder()
    {
        $this->ee['folder'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['password1'] = factory(\Lockd\Models\Password::class)->create(['folder_id' => $this->ee['folder']->id]);
        $this->ee['password2'] = factory(\Lockd\Models\Password::class)->create(['folder_id' => $this->ee['folder']->id]);
        $this->ee['password3'] = factory(\Lockd\Models\Password::class)->create(['folder_id' => $this->ee['folder']->id]);

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
}