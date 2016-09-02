<?php

class UserControllerTest extends TestCase
{
    public function tearDown()
    {
        if (count($this->ee) > 0)
            foreach ($this->ee as $entity)
                $entity->delete();

        parent::tearDown();
    }

    public function testGetAllUsers()
    {
        $this->ee['user1'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['user2'] = factory(\Lockd\Models\User::class)->create();

        $this
            ->get('/api/user')
            ->assertResponseStatus(200)
            ->seeJson([
                'data' => [
                    [
                        'id' => $this->ee['user1']->id,
                        'firstName' => $this->ee['user1']->firstName,
                        'lastName' => $this->ee['user1']->lastName,
                        'email' => $this->ee['user1']->email,
                        'created_at' => $this->ee['user1']->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $this->ee['user1']->updated_at->format('Y-m-d H:i:s'),
                    ],
                    [
                        'id' => $this->ee['user2']->id,
                        'firstName' => $this->ee['user2']->firstName,
                        'lastName' => $this->ee['user2']->lastName,
                        'email' => $this->ee['user2']->email,
                        'created_at' => $this->ee['user2']->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $this->ee['user2']->updated_at->format('Y-m-d H:i:s'),
                    ]
                ]
            ]);
    }

    public function testGetUserById()
    {
        $this->ee['user1'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['user2'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['user3'] = factory(\Lockd\Models\User::class)->create();

        $this
            ->get('/api/user?id=' . $this->ee['user2']->id)
            ->assertResponseStatus(200)
            ->seeJson([
                'data' => [
                    'id' => $this->ee['user2']->id,
                    'firstName' => $this->ee['user2']->firstName,
                    'lastName' => $this->ee['user2']->lastName,
                    'email' => $this->ee['user2']->email,
                    'created_at' => $this->ee['user2']->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $this->ee['user2']->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
    }

    public function testGetUserByEmail()
    {
        $this->ee['user1'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['user2'] = factory(\Lockd\Models\User::class)->create();
        $this->ee['user3'] = factory(\Lockd\Models\User::class)->create();

        $this
            ->get('/api/user?email=' . $this->ee['user2']->email)
            ->assertResponseStatus(200)
            ->seeJson([
                'data' => [
                    'id' => $this->ee['user2']->id,
                    'firstName' => $this->ee['user2']->firstName,
                    'lastName' => $this->ee['user2']->lastName,
                    'email' => $this->ee['user2']->email,
                    'created_at' => $this->ee['user2']->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $this->ee['user2']->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
    }
}