<?php

use \Lockd\Models\User;

class DefaultUserRepositoryTest extends TestCase
{
    /**
     * @var \Lockd\Repositories\DefaultUserRepository
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new \Lockd\Repositories\DefaultUserRepository();
    }

    public function tearDown()
    {
        if (count($this->ee) > 0)
            foreach ($this->ee as $entity)
                $entity->delete();

        unset($this->repository);
        parent::tearDown();
    }

    public function testFind()
    {
        $this->ee['user1'] = factory(User::class)->create(['firstName' => 'Fred']);
        $this->ee['user2'] = factory(User::class)->create(['firstName' => 'John']);
        $this->ee['user3'] = factory(User::class)->create(['firstName' => 'John']);
        $this->ee['user4'] = factory(User::class)->create(['firstName' => 'Mike']);

        $results = $this->repository->find([['firstName', '=', 'John']]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
        $this->assertCount(2, $results);

        $this->assertInstanceOf(User::class, $results[0]);
        $this->assertEquals($this->ee['user2']->id, $results[0]->id);

        $this->assertInstanceOf(User::class, $results[1]);
        $this->assertEquals($this->ee['user3']->id, $results[1]->id);

        unset($results);
    }

    public function testFindNoResults()
    {
        $this->ee['user1'] = factory(User::class)->create(['firstName' => 'Fred']);
        $this->ee['user2'] = factory(User::class)->create(['firstName' => 'John']);
        $this->ee['user3'] = factory(User::class)->create(['firstName' => 'John']);
        $this->ee['user4'] = factory(User::class)->create(['firstName' => 'Mike']);

        $results = $this->repository->find([['firstName', '=', 'Karl']]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
        $this->assertCount(0, $results);

        unset($results);
    }

    public function testFindNoParameters()
    {
        $this->ee['user1'] = factory(User::class)->create(['firstName' => 'Fred']);
        $this->ee['user2'] = factory(User::class)->create(['firstName' => 'John']);
        $this->ee['user3'] = factory(User::class)->create(['firstName' => 'John']);
        $this->ee['user4'] = factory(User::class)->create(['firstName' => 'Mike']);

        $results = $this->repository->find();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
        $this->assertCount(4, $results);

        for ($i = 0; $i < 4; $i++) {
            $this->assertInstanceOf(User::class, $results[$i]);
            $this->assertEquals($this->ee['user' . ($i + 1)]->id, $results[$i]->id);
            $this->assertEquals($this->ee['user' . ($i + 1)]->email, $results[$i]->email);
        }

        unset($results);
    }

    public function testFindNoParametersNoResults()
    {
        $results = $this->repository->find();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
        $this->assertCount(0, $results);

        unset($results);
    }

    public function testFindOneById()
    {
        $this->ee['user1'] = factory(User::class)->create();
        $this->ee['user2'] = factory(User::class)->create();
        $this->ee['user3'] = factory(User::class)->create();

        $result = $this->repository->findOneById($this->ee['user2']->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($this->ee['user2']->id, $result->id);
        $this->assertEquals($this->ee['user2']->email, $result->email);

        unset($result);
    }

    public function testFindOneByIdNoResult()
    {
        $this->assertNull($this->repository->findOneById(1000));
    }

    public function testFindOneByEmail()
    {
        $this->ee['user1'] = factory(User::class)->create();
        $this->ee['user2'] = factory(User::class)->create();
        $this->ee['user3'] = factory(User::class)->create();

        $result = $this->repository->findOneByEmail($this->ee['user2']->email);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($this->ee['user2']->id, $result->id);
        $this->assertEquals($this->ee['user2']->email, $result->email);

        unset($result);
    }

    public function testFindOneByEmailNoResult()
    {
        $this->assertNull($this->repository->findOneByEmail('not_an_email@lockd-test.com'));
    }

    public function testCount()
    {
        $this->ee['user1'] = factory(User::class)->create();
        $this->ee['user2'] = factory(User::class)->create();

        $result = $this->repository->count();

        $this->assertEquals(2, $result);

        unset($result);
    }

    public function testCountNoResults()
    {
        $this->assertEquals(0, $this->repository->count());
    }
}