<?php

class UserControllerTest extends FunctionalTestCase
{
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

    public function testCreate()
    {
        $data = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test@user.com',
            'password' => 'letmein',
            'password_confirmation' => 'letmein',
        ];

        $this
            ->put('/api/user', $data)
            ->assertResponseStatus(201)
            ->seeJson([
                'data' => 'User created successfully',
            ]);

        unset($data['password']);
        unset($data['password_confirmation']);

        $this->seeInDatabase('au_user', $data);

        $user = \Lockd\Models\User::where('email', 'test@user.com')->first();

        if ($user)
            $this->ee['user'] = $user;
    }

    public function testCreateBadRequest()
    {
        $this
            ->put('/api/user', [])
            ->assertResponseStatus(400)
            ->seeJson([
                'error' => 'bad_request',
                'errorDescription' => [
                    'The email field is required.',
                    'The first name field is required.',
                    'The last name field is required.',
                    'The password field is required.'
                ],
            ]);
    }

    public function testCreateConflict()
    {
        $data = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test@user.com',
            'password' => 'letmein',
            'password_confirmation' => 'letmein',
        ];

        $this->ee['user'] = factory(\Lockd\Models\User::class)->create(['email' => $data['email']]);

        $this
            ->put('/api/user', $data)
            ->assertResponseStatus(409)
            ->seeJson([
                'error' => 'conflict',
                'errorDescription' => 'A user already exists with that email!',
            ]);

        unset($data['password']);
        unset($data['password_confirmation']);

        $this->dontSeeInDatabase('au_user', $data);
    }
}