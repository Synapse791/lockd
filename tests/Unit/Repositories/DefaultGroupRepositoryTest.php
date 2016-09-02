<?php

use \Lockd\Models\Group;

class DefaultGroupRepositoryTest extends TestCase
{
    /**
     * @var \Lockd\Repositories\DefaultGroupRepository
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new \Lockd\Repositories\DefaultGroupRepository();
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
        $this->ee['group1'] = factory(Group::class)->create();
        $this->ee['group2'] = factory(Group::class)->create();
        $this->ee['group3'] = factory(Group::class)->create();
        $this->ee['group4'] = factory(Group::class)->create();

        $results = $this->repository->find([
            [
                'id',
                '<',
                $this->ee['group3']->id],
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
        $this->assertCount(2, $results);

        $this->assertInstanceOf(Group::class, $results[0]);
        $this->assertEquals($this->ee['group1']->id, $results[0]->id);

        $this->assertInstanceOf(Group::class, $results[1]);
        $this->assertEquals($this->ee['group2']->id, $results[1]->id);

        unset($results);
    }

    public function testFinNoParameters()
    {
        $this->ee['group1'] = factory(Group::class)->create();
        $this->ee['group2'] = factory(Group::class)->create();
        $this->ee['group3'] = factory(Group::class)->create();
        $this->ee['group4'] = factory(Group::class)->create();

        $results = $this->repository->find();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
        $this->assertCount(4, $results);

        $this->assertInstanceOf(Group::class, $results[0]);
        $this->assertEquals($this->ee['group1']->id, $results[0]->id);

        $this->assertInstanceOf(Group::class, $results[1]);
        $this->assertEquals($this->ee['group2']->id, $results[1]->id);

        $this->assertInstanceOf(Group::class, $results[2]);
        $this->assertEquals($this->ee['group3']->id, $results[2]->id);

        $this->assertInstanceOf(Group::class, $results[3]);
        $this->assertEquals($this->ee['group4']->id, $results[3]->id);

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
        $this->ee['group1'] = factory(Group::class)->create();
        $this->ee['group2'] = factory(Group::class)->create();

        $result = $this->repository->findOneById($this->ee['group2']->id);

        $this->assertInstanceOf(Group::class, $result);
        $this->assertEquals($this->ee['group2']->id, $result->id);
        $this->assertEquals($this->ee['group2']->email, $result->email);

        unset($result);
    }

    public function testFindOneByIdNoResult()
    {
        $this->assertNull($this->repository->findOneById(1000));
    }

    public function testFindOneByName()
    {
        $this->ee['group1'] = factory(Group::class)->create();
        $this->ee['group2'] = factory(Group::class)->create();

        $result = $this->repository->findOneByName($this->ee['group2']->name);

        $this->assertInstanceOf(Group::class, $result);
        $this->assertEquals($this->ee['group2']->id, $result->id);
        $this->assertEquals($this->ee['group2']->email, $result->email);

        unset($result);
    }

    public function testFindOneByNameNoResult()
    {
        $this->assertNull($this->repository->findOneByName(1000));
    }

    public function testCount()
    {
        $this->ee['group1'] = factory(Group::class)->create();
        $this->ee['group2'] = factory(Group::class)->create();

        $result = $this->repository->count();

        $this->assertEquals(2, $result);

        unset($result);
    }

    public function testCountNoResults()
    {
        $this->assertEquals(0, $this->repository->count());
    }
}