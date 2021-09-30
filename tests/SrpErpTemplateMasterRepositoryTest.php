<?php namespace Tests\Repositories;

use App\Models\SrpErpTemplateMaster;
use App\Repositories\SrpErpTemplateMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrpErpTemplateMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrpErpTemplateMasterRepository
     */
    protected $srpErpTemplateMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srpErpTemplateMasterRepo = \App::make(SrpErpTemplateMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srp_erp_template_master()
    {
        $srpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->make()->toArray();

        $createdSrpErpTemplateMaster = $this->srpErpTemplateMasterRepo->create($srpErpTemplateMaster);

        $createdSrpErpTemplateMaster = $createdSrpErpTemplateMaster->toArray();
        $this->assertArrayHasKey('id', $createdSrpErpTemplateMaster);
        $this->assertNotNull($createdSrpErpTemplateMaster['id'], 'Created SrpErpTemplateMaster must have id specified');
        $this->assertNotNull(SrpErpTemplateMaster::find($createdSrpErpTemplateMaster['id']), 'SrpErpTemplateMaster with given id must be in DB');
        $this->assertModelData($srpErpTemplateMaster, $createdSrpErpTemplateMaster);
    }

    /**
     * @test read
     */
    public function test_read_srp_erp_template_master()
    {
        $srpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->create();

        $dbSrpErpTemplateMaster = $this->srpErpTemplateMasterRepo->find($srpErpTemplateMaster->id);

        $dbSrpErpTemplateMaster = $dbSrpErpTemplateMaster->toArray();
        $this->assertModelData($srpErpTemplateMaster->toArray(), $dbSrpErpTemplateMaster);
    }

    /**
     * @test update
     */
    public function test_update_srp_erp_template_master()
    {
        $srpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->create();
        $fakeSrpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->make()->toArray();

        $updatedSrpErpTemplateMaster = $this->srpErpTemplateMasterRepo->update($fakeSrpErpTemplateMaster, $srpErpTemplateMaster->id);

        $this->assertModelData($fakeSrpErpTemplateMaster, $updatedSrpErpTemplateMaster->toArray());
        $dbSrpErpTemplateMaster = $this->srpErpTemplateMasterRepo->find($srpErpTemplateMaster->id);
        $this->assertModelData($fakeSrpErpTemplateMaster, $dbSrpErpTemplateMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srp_erp_template_master()
    {
        $srpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->create();

        $resp = $this->srpErpTemplateMasterRepo->delete($srpErpTemplateMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SrpErpTemplateMaster::find($srpErpTemplateMaster->id), 'SrpErpTemplateMaster should not exist in DB');
    }
}
