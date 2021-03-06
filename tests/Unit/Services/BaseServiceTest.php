<?php

use \Lockd\Services\BaseService;

class BaseServiceTest extends TestCase
{
    /** @var \Lockd\Services\BaseService */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new \Lockd\Services\BaseService();
    }

    public function tearDown()
    {
        unset($this->service);
        parent::tearDown();
    }

    public function testSetError()
    {
        $this->assertInstanceOf(
            BaseService::class,
            $this->service->setError('test_error')
        );

        $this->assertEquals(
            'test_error',
            $this->service->getError()
        );
    }

    public function testSetErrorCode()
    {
        $this->assertInstanceOf(
            BaseService::class,
            $this->service->setErrorCode(400)
        );

        $this->assertEquals(
            400,
            $this->service->getErrorCode()
        );
    }

    public function testSetErrorDescription()
    {
        $this->assertInstanceOf(
            BaseService::class,
            $this->service->setErrorDescription('Test Description')
        );

        $this->assertEquals(
            'Test Description',
            $this->service->getErrorDescription()
        );
    }

    public function testSetBadRequestError()
    {
        $this->assertFalse($this->service->setBadRequestError('Bad Request Description'));

        $this->assertEquals('bad_request', $this->service->getError());
        $this->assertEquals(400, $this->service->getErrorCode());
        $this->assertEquals('Bad Request Description', $this->service->getErrorDescription());
    }

    public function testSetNotFoundError()
    {
        $this->assertFalse($this->service->setNotFoundError('Not Found Description'));

        $this->assertEquals('not_found', $this->service->getError());
        $this->assertEquals(404, $this->service->getErrorCode());
        $this->assertEquals('Not Found Description', $this->service->getErrorDescription());
    }

    public function testSetConflictError()
    {
        $this->assertFalse($this->service->setConflictError('Conflict Description'));

        $this->assertEquals('conflict', $this->service->getError());
        $this->assertEquals(409, $this->service->getErrorCode());
        $this->assertEquals('Conflict Description', $this->service->getErrorDescription());
    }

    public function testSetInternalServerError()
    {
        $this->assertFalse($this->service->setInternalServerError('Internal Server Error Description'));

        $this->assertEquals('internal_server_error', $this->service->getError());
        $this->assertEquals(500, $this->service->getErrorCode());
        $this->assertEquals('Internal Server Error Description', $this->service->getErrorDescription());
    }

    public function testSaveEntity()
    {
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
        $mockEntity
            ->shouldReceive('save')
            ->andReturn(true);

        $this->assertTrue($this->service->saveEntity($mockEntity));
    }

    public function testSaveEntityDuplicateException()
    {
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
        $mockEntity
            ->shouldReceive('save')
            ->andThrow(\PDOException::class, 'blah blah blah Duplicate entry blah blah blah');

        $this->assertFalse($this->service->saveEntity($mockEntity));

        $this->assertEquals('conflict', $this->service->getError());
        $this->assertEquals(409, $this->service->getErrorCode());
        $this->assertEquals(get_class($mockEntity) . ' already exists', $this->service->getErrorDescription());
    }

    public function testSaveEntityDuplicateExceptionWithCustomError()
    {
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
        $mockEntity
            ->shouldReceive('save')
            ->andThrow(\PDOException::class, 'blah blah blah Duplicate entry blah blah blah');

        $this->assertFalse($this->service->saveEntity($mockEntity, 'That entity already exists!'));

        $this->assertEquals('conflict', $this->service->getError());
        $this->assertEquals(409, $this->service->getErrorCode());
        $this->assertEquals('That entity already exists!', $this->service->getErrorDescription());
    }

    public function testSaveEntityCatchAllDatabaseError()
    {
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
        $mockEntity
            ->shouldReceive('save')
            ->andThrow(\PDOException::class, 'blah blah blah Something is wrong blah blah blah');

        $this->assertFalse($this->service->saveEntity($mockEntity));

        $this->assertEquals('internal_server_error', $this->service->getError());
        $this->assertEquals(500, $this->service->getErrorCode());
        $this->assertEquals('blah blah blah Something is wrong blah blah blah', $this->service->getErrorDescription());
    }

    public function testAssociateEntities()
    {
        $mockRelationship = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);

        $mockRelationship
            ->shouldReceive('associate')
            ->with($mockEntity)
            ->andReturn(true);

        $this->assertTrue(
            $this->service->associateEntities($mockRelationship, $mockEntity)
        );
    }

    public function testAssociateEntitiesException()
    {
        $mockRelationship = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);

        $mockRelationship
            ->shouldReceive('associate')
            ->with($mockEntity)
            ->andThrow(\PDOException::class, 'PDO Exception thrown');

        $this->assertFalse(
            $this->service->associateEntities($mockRelationship, $mockEntity)
        );

        $this->assertEquals('internal_server_error', $this->service->getError());
        $this->assertEquals(500, $this->service->getErrorCode());
        $this->assertEquals('PDO Exception thrown', $this->service->getErrorDescription());
    }

    public function testDissociateEntities()
    {
        $mockRelationship = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);

        $mockRelationship
            ->shouldReceive('dissociate')
            ->andReturn(true);

        $this->assertTrue(
            $this->service->dissociateEntities($mockRelationship)
        );
    }

    public function testDissociateEntitiesException()
    {
        $mockRelationship = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);


        $mockRelationship
            ->shouldReceive('dissociate')
            ->andThrow(\PDOException::class, 'PDO Exception thrown');

        $this->assertFalse(
            $this->service->dissociateEntities($mockRelationship)
        );

        $this->assertEquals('internal_server_error', $this->service->getError());
        $this->assertEquals(500, $this->service->getErrorCode());
        $this->assertEquals('PDO Exception thrown', $this->service->getErrorDescription());
    }

    public function testAttachEntities()
    {
        $mockRelationship = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);

        $mockRelationship
            ->shouldReceive('attach')
            ->with($mockEntity)
            ->andReturn(true);

        $this->assertTrue(
            $this->service->attachEntities($mockRelationship, $mockEntity)
        );
    }

    public function testAttachEntitiesException()
    {
        $mockRelationship = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);

        $mockRelationship
            ->shouldReceive('attach')
            ->with($mockEntity)
            ->andThrow(\PDOException::class, 'PDO Exception thrown');

        $this->assertFalse(
            $this->service->attachEntities($mockRelationship, $mockEntity)
        );

        $this->assertEquals('internal_server_error', $this->service->getError());
        $this->assertEquals(500, $this->service->getErrorCode());
        $this->assertEquals('PDO Exception thrown', $this->service->getErrorDescription());
    }

    public function testDetachEntities()
    {
        $mockRelationship = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);

        $mockRelationship
            ->shouldReceive('detach')
            ->with($mockEntity)
            ->andReturn(true);

        $this->assertTrue(
            $this->service->detachEntities($mockRelationship, $mockEntity)
        );
    }

    public function testDetachEntitiesException()
    {
        $mockRelationship = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
        $mockEntity = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);

        $mockRelationship
            ->shouldReceive('detach')
            ->with($mockEntity)
            ->andThrow(\PDOException::class, 'PDO Exception thrown');

        $this->assertFalse(
            $this->service->detachEntities($mockRelationship, $mockEntity)
        );

        $this->assertEquals('internal_server_error', $this->service->getError());
        $this->assertEquals(500, $this->service->getErrorCode());
        $this->assertEquals('PDO Exception thrown', $this->service->getErrorDescription());
    }
}