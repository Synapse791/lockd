<?php

class FunctionalTestCase extends TestCase
{
    protected $authUser;

    public function setUp()
    {
        parent::setUp();
        $this->authUser = new \Lockd\Models\User([
            'firstName' => 'Auth',
            'lastName' => 'User',
            'email' => 'auth@user.com',
            'password' => 'letmein',
        ]);
        $this->be($this->authUser);
    }

    public function tearDown()
    {
        if (count($this->ee) > 0)
            foreach ($this->ee as $entity)
                $entity->delete();

        unset($this->authUser);
        parent::tearDown();
    }
}
