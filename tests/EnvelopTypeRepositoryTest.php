<?php namespace Tests\Repositories;

use App\Models\EnvelopType;
use App\Repositories\EnvelopTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EnvelopTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EnvelopTypeRepository
     */
    protected $envelopTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->envelopTypeRepo = \App::make(EnvelopTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_envelop_type()
    {
        $envelopType = factory(EnvelopType::class)->make()->toArray();

        $createdEnvelopType = $this->envelopTypeRepo->create($envelopType);

        $createdEnvelopType = $createdEnvelopType->toArray();
        $this->assertArrayHasKey('id', $createdEnvelopType);
        $this->assertNotNull($createdEnvelopType['id'], 'Created EnvelopType must have id specified');
        $this->assertNotNull(EnvelopType::find($createdEnvelopType['id']), 'EnvelopType with given id must be in DB');
        $this->assertModelData($envelopType, $createdEnvelopType);
    }

    /**
     * @test read
     */
    public function test_read_envelop_type()
    {
        $envelopType = factory(EnvelopType::class)->create();

        $dbEnvelopType = $this->envelopTypeRepo->find($envelopType->id);

        $dbEnvelopType = $dbEnvelopType->toArray();
        $this->assertModelData($envelopType->toArray(), $dbEnvelopType);
    }

    /**
     * @test update
     */
    public function test_update_envelop_type()
    {
        $envelopType = factory(EnvelopType::class)->create();
        $fakeEnvelopType = factory(EnvelopType::class)->make()->toArray();

        $updatedEnvelopType = $this->envelopTypeRepo->update($fakeEnvelopType, $envelopType->id);

        $this->assertModelData($fakeEnvelopType, $updatedEnvelopType->toArray());
        $dbEnvelopType = $this->envelopTypeRepo->find($envelopType->id);
        $this->assertModelData($fakeEnvelopType, $dbEnvelopType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_envelop_type()
    {
        $envelopType = factory(EnvelopType::class)->create();

        $resp = $this->envelopTypeRepo->delete($envelopType->id);

        $this->assertTrue($resp);
        $this->assertNull(EnvelopType::find($envelopType->id), 'EnvelopType should not exist in DB');
    }
}
