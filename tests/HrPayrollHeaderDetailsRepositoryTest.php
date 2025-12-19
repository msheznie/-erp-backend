<?php namespace Tests\Repositories;

use App\Models\HrPayrollHeaderDetails;
use App\Repositories\HrPayrollHeaderDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrPayrollHeaderDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrPayrollHeaderDetailsRepository
     */
    protected $hrPayrollHeaderDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrPayrollHeaderDetailsRepo = \App::make(HrPayrollHeaderDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_payroll_header_details()
    {
        $hrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->make()->toArray();

        $createdHrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepo->create($hrPayrollHeaderDetails);

        $createdHrPayrollHeaderDetails = $createdHrPayrollHeaderDetails->toArray();
        $this->assertArrayHasKey('id', $createdHrPayrollHeaderDetails);
        $this->assertNotNull($createdHrPayrollHeaderDetails['id'], 'Created HrPayrollHeaderDetails must have id specified');
        $this->assertNotNull(HrPayrollHeaderDetails::find($createdHrPayrollHeaderDetails['id']), 'HrPayrollHeaderDetails with given id must be in DB');
        $this->assertModelData($hrPayrollHeaderDetails, $createdHrPayrollHeaderDetails);
    }

    /**
     * @test read
     */
    public function test_read_hr_payroll_header_details()
    {
        $hrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->create();

        $dbHrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepo->find($hrPayrollHeaderDetails->id);

        $dbHrPayrollHeaderDetails = $dbHrPayrollHeaderDetails->toArray();
        $this->assertModelData($hrPayrollHeaderDetails->toArray(), $dbHrPayrollHeaderDetails);
    }

    /**
     * @test update
     */
    public function test_update_hr_payroll_header_details()
    {
        $hrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->create();
        $fakeHrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->make()->toArray();

        $updatedHrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepo->update($fakeHrPayrollHeaderDetails, $hrPayrollHeaderDetails->id);

        $this->assertModelData($fakeHrPayrollHeaderDetails, $updatedHrPayrollHeaderDetails->toArray());
        $dbHrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepo->find($hrPayrollHeaderDetails->id);
        $this->assertModelData($fakeHrPayrollHeaderDetails, $dbHrPayrollHeaderDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_payroll_header_details()
    {
        $hrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->create();

        $resp = $this->hrPayrollHeaderDetailsRepo->delete($hrPayrollHeaderDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(HrPayrollHeaderDetails::find($hrPayrollHeaderDetails->id), 'HrPayrollHeaderDetails should not exist in DB');
    }
}
