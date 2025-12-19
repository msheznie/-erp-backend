<?php namespace Tests\Repositories;

use App\Models\HrMonthlyDeductionDetail;
use App\Repositories\HrMonthlyDeductionDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrMonthlyDeductionDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrMonthlyDeductionDetailRepository
     */
    protected $hrMonthlyDeductionDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrMonthlyDeductionDetailRepo = \App::make(HrMonthlyDeductionDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_monthly_deduction_detail()
    {
        $hrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->make()->toArray();

        $createdHrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepo->create($hrMonthlyDeductionDetail);

        $createdHrMonthlyDeductionDetail = $createdHrMonthlyDeductionDetail->toArray();
        $this->assertArrayHasKey('id', $createdHrMonthlyDeductionDetail);
        $this->assertNotNull($createdHrMonthlyDeductionDetail['id'], 'Created HrMonthlyDeductionDetail must have id specified');
        $this->assertNotNull(HrMonthlyDeductionDetail::find($createdHrMonthlyDeductionDetail['id']), 'HrMonthlyDeductionDetail with given id must be in DB');
        $this->assertModelData($hrMonthlyDeductionDetail, $createdHrMonthlyDeductionDetail);
    }

    /**
     * @test read
     */
    public function test_read_hr_monthly_deduction_detail()
    {
        $hrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->create();

        $dbHrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepo->find($hrMonthlyDeductionDetail->id);

        $dbHrMonthlyDeductionDetail = $dbHrMonthlyDeductionDetail->toArray();
        $this->assertModelData($hrMonthlyDeductionDetail->toArray(), $dbHrMonthlyDeductionDetail);
    }

    /**
     * @test update
     */
    public function test_update_hr_monthly_deduction_detail()
    {
        $hrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->create();
        $fakeHrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->make()->toArray();

        $updatedHrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepo->update($fakeHrMonthlyDeductionDetail, $hrMonthlyDeductionDetail->id);

        $this->assertModelData($fakeHrMonthlyDeductionDetail, $updatedHrMonthlyDeductionDetail->toArray());
        $dbHrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepo->find($hrMonthlyDeductionDetail->id);
        $this->assertModelData($fakeHrMonthlyDeductionDetail, $dbHrMonthlyDeductionDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_monthly_deduction_detail()
    {
        $hrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->create();

        $resp = $this->hrMonthlyDeductionDetailRepo->delete($hrMonthlyDeductionDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(HrMonthlyDeductionDetail::find($hrMonthlyDeductionDetail->id), 'HrMonthlyDeductionDetail should not exist in DB');
    }
}
