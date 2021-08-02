<?php namespace Tests\Repositories;

use App\Models\HrMonthlyDeductionMaster;
use App\Repositories\HrMonthlyDeductionMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrMonthlyDeductionMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrMonthlyDeductionMasterRepository
     */
    protected $hrMonthlyDeductionMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrMonthlyDeductionMasterRepo = \App::make(HrMonthlyDeductionMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_monthly_deduction_master()
    {
        $hrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->make()->toArray();

        $createdHrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepo->create($hrMonthlyDeductionMaster);

        $createdHrMonthlyDeductionMaster = $createdHrMonthlyDeductionMaster->toArray();
        $this->assertArrayHasKey('id', $createdHrMonthlyDeductionMaster);
        $this->assertNotNull($createdHrMonthlyDeductionMaster['id'], 'Created HrMonthlyDeductionMaster must have id specified');
        $this->assertNotNull(HrMonthlyDeductionMaster::find($createdHrMonthlyDeductionMaster['id']), 'HrMonthlyDeductionMaster with given id must be in DB');
        $this->assertModelData($hrMonthlyDeductionMaster, $createdHrMonthlyDeductionMaster);
    }

    /**
     * @test read
     */
    public function test_read_hr_monthly_deduction_master()
    {
        $hrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->create();

        $dbHrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepo->find($hrMonthlyDeductionMaster->id);

        $dbHrMonthlyDeductionMaster = $dbHrMonthlyDeductionMaster->toArray();
        $this->assertModelData($hrMonthlyDeductionMaster->toArray(), $dbHrMonthlyDeductionMaster);
    }

    /**
     * @test update
     */
    public function test_update_hr_monthly_deduction_master()
    {
        $hrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->create();
        $fakeHrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->make()->toArray();

        $updatedHrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepo->update($fakeHrMonthlyDeductionMaster, $hrMonthlyDeductionMaster->id);

        $this->assertModelData($fakeHrMonthlyDeductionMaster, $updatedHrMonthlyDeductionMaster->toArray());
        $dbHrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepo->find($hrMonthlyDeductionMaster->id);
        $this->assertModelData($fakeHrMonthlyDeductionMaster, $dbHrMonthlyDeductionMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_monthly_deduction_master()
    {
        $hrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->create();

        $resp = $this->hrMonthlyDeductionMasterRepo->delete($hrMonthlyDeductionMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(HrMonthlyDeductionMaster::find($hrMonthlyDeductionMaster->id), 'HrMonthlyDeductionMaster should not exist in DB');
    }
}
