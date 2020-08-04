<?php namespace Tests\Repositories;

use App\Models\CustomReportMaster;
use App\Repositories\CustomReportMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomReportMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomReportMasterRepository
     */
    protected $customReportMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customReportMasterRepo = \App::make(CustomReportMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_custom_report_master()
    {
        $customReportMaster = factory(CustomReportMaster::class)->make()->toArray();

        $createdCustomReportMaster = $this->customReportMasterRepo->create($customReportMaster);

        $createdCustomReportMaster = $createdCustomReportMaster->toArray();
        $this->assertArrayHasKey('id', $createdCustomReportMaster);
        $this->assertNotNull($createdCustomReportMaster['id'], 'Created CustomReportMaster must have id specified');
        $this->assertNotNull(CustomReportMaster::find($createdCustomReportMaster['id']), 'CustomReportMaster with given id must be in DB');
        $this->assertModelData($customReportMaster, $createdCustomReportMaster);
    }

    /**
     * @test read
     */
    public function test_read_custom_report_master()
    {
        $customReportMaster = factory(CustomReportMaster::class)->create();

        $dbCustomReportMaster = $this->customReportMasterRepo->find($customReportMaster->id);

        $dbCustomReportMaster = $dbCustomReportMaster->toArray();
        $this->assertModelData($customReportMaster->toArray(), $dbCustomReportMaster);
    }

    /**
     * @test update
     */
    public function test_update_custom_report_master()
    {
        $customReportMaster = factory(CustomReportMaster::class)->create();
        $fakeCustomReportMaster = factory(CustomReportMaster::class)->make()->toArray();

        $updatedCustomReportMaster = $this->customReportMasterRepo->update($fakeCustomReportMaster, $customReportMaster->id);

        $this->assertModelData($fakeCustomReportMaster, $updatedCustomReportMaster->toArray());
        $dbCustomReportMaster = $this->customReportMasterRepo->find($customReportMaster->id);
        $this->assertModelData($fakeCustomReportMaster, $dbCustomReportMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_custom_report_master()
    {
        $customReportMaster = factory(CustomReportMaster::class)->create();

        $resp = $this->customReportMasterRepo->delete($customReportMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomReportMaster::find($customReportMaster->id), 'CustomReportMaster should not exist in DB');
    }
}
