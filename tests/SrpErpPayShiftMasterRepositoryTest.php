<?php namespace Tests\Repositories;

use App\Models\SrpErpPayShiftMaster;
use App\Repositories\SrpErpPayShiftMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrpErpPayShiftMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrpErpPayShiftMasterRepository
     */
    protected $srpErpPayShiftMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srpErpPayShiftMasterRepo = \App::make(SrpErpPayShiftMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srp_erp_pay_shift_master()
    {
        $srpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->make()->toArray();

        $createdSrpErpPayShiftMaster = $this->srpErpPayShiftMasterRepo->create($srpErpPayShiftMaster);

        $createdSrpErpPayShiftMaster = $createdSrpErpPayShiftMaster->toArray();
        $this->assertArrayHasKey('id', $createdSrpErpPayShiftMaster);
        $this->assertNotNull($createdSrpErpPayShiftMaster['id'], 'Created SrpErpPayShiftMaster must have id specified');
        $this->assertNotNull(SrpErpPayShiftMaster::find($createdSrpErpPayShiftMaster['id']), 'SrpErpPayShiftMaster with given id must be in DB');
        $this->assertModelData($srpErpPayShiftMaster, $createdSrpErpPayShiftMaster);
    }

    /**
     * @test read
     */
    public function test_read_srp_erp_pay_shift_master()
    {
        $srpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->create();

        $dbSrpErpPayShiftMaster = $this->srpErpPayShiftMasterRepo->find($srpErpPayShiftMaster->id);

        $dbSrpErpPayShiftMaster = $dbSrpErpPayShiftMaster->toArray();
        $this->assertModelData($srpErpPayShiftMaster->toArray(), $dbSrpErpPayShiftMaster);
    }

    /**
     * @test update
     */
    public function test_update_srp_erp_pay_shift_master()
    {
        $srpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->create();
        $fakeSrpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->make()->toArray();

        $updatedSrpErpPayShiftMaster = $this->srpErpPayShiftMasterRepo->update($fakeSrpErpPayShiftMaster, $srpErpPayShiftMaster->id);

        $this->assertModelData($fakeSrpErpPayShiftMaster, $updatedSrpErpPayShiftMaster->toArray());
        $dbSrpErpPayShiftMaster = $this->srpErpPayShiftMasterRepo->find($srpErpPayShiftMaster->id);
        $this->assertModelData($fakeSrpErpPayShiftMaster, $dbSrpErpPayShiftMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srp_erp_pay_shift_master()
    {
        $srpErpPayShiftMaster = factory(SrpErpPayShiftMaster::class)->create();

        $resp = $this->srpErpPayShiftMasterRepo->delete($srpErpPayShiftMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SrpErpPayShiftMaster::find($srpErpPayShiftMaster->id), 'SrpErpPayShiftMaster should not exist in DB');
    }
}
