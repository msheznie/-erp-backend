<?php namespace Tests\Repositories;

use App\Models\StagCustomerTypeMaster;
use App\Repositories\StagCustomerTypeMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class StagCustomerTypeMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var StagCustomerTypeMasterRepository
     */
    protected $stagCustomerTypeMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->stagCustomerTypeMasterRepo = \App::make(StagCustomerTypeMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_stag_customer_type_master()
    {
        $stagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->make()->toArray();

        $createdStagCustomerTypeMaster = $this->stagCustomerTypeMasterRepo->create($stagCustomerTypeMaster);

        $createdStagCustomerTypeMaster = $createdStagCustomerTypeMaster->toArray();
        $this->assertArrayHasKey('id', $createdStagCustomerTypeMaster);
        $this->assertNotNull($createdStagCustomerTypeMaster['id'], 'Created StagCustomerTypeMaster must have id specified');
        $this->assertNotNull(StagCustomerTypeMaster::find($createdStagCustomerTypeMaster['id']), 'StagCustomerTypeMaster with given id must be in DB');
        $this->assertModelData($stagCustomerTypeMaster, $createdStagCustomerTypeMaster);
    }

    /**
     * @test read
     */
    public function test_read_stag_customer_type_master()
    {
        $stagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->create();

        $dbStagCustomerTypeMaster = $this->stagCustomerTypeMasterRepo->find($stagCustomerTypeMaster->id);

        $dbStagCustomerTypeMaster = $dbStagCustomerTypeMaster->toArray();
        $this->assertModelData($stagCustomerTypeMaster->toArray(), $dbStagCustomerTypeMaster);
    }

    /**
     * @test update
     */
    public function test_update_stag_customer_type_master()
    {
        $stagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->create();
        $fakeStagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->make()->toArray();

        $updatedStagCustomerTypeMaster = $this->stagCustomerTypeMasterRepo->update($fakeStagCustomerTypeMaster, $stagCustomerTypeMaster->id);

        $this->assertModelData($fakeStagCustomerTypeMaster, $updatedStagCustomerTypeMaster->toArray());
        $dbStagCustomerTypeMaster = $this->stagCustomerTypeMasterRepo->find($stagCustomerTypeMaster->id);
        $this->assertModelData($fakeStagCustomerTypeMaster, $dbStagCustomerTypeMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_stag_customer_type_master()
    {
        $stagCustomerTypeMaster = factory(StagCustomerTypeMaster::class)->create();

        $resp = $this->stagCustomerTypeMasterRepo->delete($stagCustomerTypeMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(StagCustomerTypeMaster::find($stagCustomerTypeMaster->id), 'StagCustomerTypeMaster should not exist in DB');
    }
}
