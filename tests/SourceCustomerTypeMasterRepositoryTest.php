<?php namespace Tests\Repositories;

use App\Models\SourceCustomerTypeMaster;
use App\Repositories\SourceCustomerTypeMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SourceCustomerTypeMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SourceCustomerTypeMasterRepository
     */
    protected $sourceCustomerTypeMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sourceCustomerTypeMasterRepo = \App::make(SourceCustomerTypeMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_source_customer_type_master()
    {
        $sourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->make()->toArray();

        $createdSourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepo->create($sourceCustomerTypeMaster);

        $createdSourceCustomerTypeMaster = $createdSourceCustomerTypeMaster->toArray();
        $this->assertArrayHasKey('id', $createdSourceCustomerTypeMaster);
        $this->assertNotNull($createdSourceCustomerTypeMaster['id'], 'Created SourceCustomerTypeMaster must have id specified');
        $this->assertNotNull(SourceCustomerTypeMaster::find($createdSourceCustomerTypeMaster['id']), 'SourceCustomerTypeMaster with given id must be in DB');
        $this->assertModelData($sourceCustomerTypeMaster, $createdSourceCustomerTypeMaster);
    }

    /**
     * @test read
     */
    public function test_read_source_customer_type_master()
    {
        $sourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->create();

        $dbSourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepo->find($sourceCustomerTypeMaster->id);

        $dbSourceCustomerTypeMaster = $dbSourceCustomerTypeMaster->toArray();
        $this->assertModelData($sourceCustomerTypeMaster->toArray(), $dbSourceCustomerTypeMaster);
    }

    /**
     * @test update
     */
    public function test_update_source_customer_type_master()
    {
        $sourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->create();
        $fakeSourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->make()->toArray();

        $updatedSourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepo->update($fakeSourceCustomerTypeMaster, $sourceCustomerTypeMaster->id);

        $this->assertModelData($fakeSourceCustomerTypeMaster, $updatedSourceCustomerTypeMaster->toArray());
        $dbSourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepo->find($sourceCustomerTypeMaster->id);
        $this->assertModelData($fakeSourceCustomerTypeMaster, $dbSourceCustomerTypeMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_source_customer_type_master()
    {
        $sourceCustomerTypeMaster = factory(SourceCustomerTypeMaster::class)->create();

        $resp = $this->sourceCustomerTypeMasterRepo->delete($sourceCustomerTypeMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SourceCustomerTypeMaster::find($sourceCustomerTypeMaster->id), 'SourceCustomerTypeMaster should not exist in DB');
    }
}
