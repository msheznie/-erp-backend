<?php namespace Tests\Repositories;

use App\Models\MobileBillMaster;
use App\Repositories\MobileBillMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MobileBillMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MobileBillMasterRepository
     */
    protected $mobileBillMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->mobileBillMasterRepo = \App::make(MobileBillMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mobile_bill_master()
    {
        $mobileBillMaster = factory(MobileBillMaster::class)->make()->toArray();

        $createdMobileBillMaster = $this->mobileBillMasterRepo->create($mobileBillMaster);

        $createdMobileBillMaster = $createdMobileBillMaster->toArray();
        $this->assertArrayHasKey('id', $createdMobileBillMaster);
        $this->assertNotNull($createdMobileBillMaster['id'], 'Created MobileBillMaster must have id specified');
        $this->assertNotNull(MobileBillMaster::find($createdMobileBillMaster['id']), 'MobileBillMaster with given id must be in DB');
        $this->assertModelData($mobileBillMaster, $createdMobileBillMaster);
    }

    /**
     * @test read
     */
    public function test_read_mobile_bill_master()
    {
        $mobileBillMaster = factory(MobileBillMaster::class)->create();

        $dbMobileBillMaster = $this->mobileBillMasterRepo->find($mobileBillMaster->id);

        $dbMobileBillMaster = $dbMobileBillMaster->toArray();
        $this->assertModelData($mobileBillMaster->toArray(), $dbMobileBillMaster);
    }

    /**
     * @test update
     */
    public function test_update_mobile_bill_master()
    {
        $mobileBillMaster = factory(MobileBillMaster::class)->create();
        $fakeMobileBillMaster = factory(MobileBillMaster::class)->make()->toArray();

        $updatedMobileBillMaster = $this->mobileBillMasterRepo->update($fakeMobileBillMaster, $mobileBillMaster->id);

        $this->assertModelData($fakeMobileBillMaster, $updatedMobileBillMaster->toArray());
        $dbMobileBillMaster = $this->mobileBillMasterRepo->find($mobileBillMaster->id);
        $this->assertModelData($fakeMobileBillMaster, $dbMobileBillMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mobile_bill_master()
    {
        $mobileBillMaster = factory(MobileBillMaster::class)->create();

        $resp = $this->mobileBillMasterRepo->delete($mobileBillMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(MobileBillMaster::find($mobileBillMaster->id), 'MobileBillMaster should not exist in DB');
    }
}
