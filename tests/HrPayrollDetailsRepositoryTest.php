<?php namespace Tests\Repositories;

use App\Models\HrPayrollDetails;
use App\Repositories\HrPayrollDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrPayrollDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrPayrollDetailsRepository
     */
    protected $hrPayrollDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrPayrollDetailsRepo = \App::make(HrPayrollDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_payroll_details()
    {
        $hrPayrollDetails = factory(HrPayrollDetails::class)->make()->toArray();

        $createdHrPayrollDetails = $this->hrPayrollDetailsRepo->create($hrPayrollDetails);

        $createdHrPayrollDetails = $createdHrPayrollDetails->toArray();
        $this->assertArrayHasKey('id', $createdHrPayrollDetails);
        $this->assertNotNull($createdHrPayrollDetails['id'], 'Created HrPayrollDetails must have id specified');
        $this->assertNotNull(HrPayrollDetails::find($createdHrPayrollDetails['id']), 'HrPayrollDetails with given id must be in DB');
        $this->assertModelData($hrPayrollDetails, $createdHrPayrollDetails);
    }

    /**
     * @test read
     */
    public function test_read_hr_payroll_details()
    {
        $hrPayrollDetails = factory(HrPayrollDetails::class)->create();

        $dbHrPayrollDetails = $this->hrPayrollDetailsRepo->find($hrPayrollDetails->id);

        $dbHrPayrollDetails = $dbHrPayrollDetails->toArray();
        $this->assertModelData($hrPayrollDetails->toArray(), $dbHrPayrollDetails);
    }

    /**
     * @test update
     */
    public function test_update_hr_payroll_details()
    {
        $hrPayrollDetails = factory(HrPayrollDetails::class)->create();
        $fakeHrPayrollDetails = factory(HrPayrollDetails::class)->make()->toArray();

        $updatedHrPayrollDetails = $this->hrPayrollDetailsRepo->update($fakeHrPayrollDetails, $hrPayrollDetails->id);

        $this->assertModelData($fakeHrPayrollDetails, $updatedHrPayrollDetails->toArray());
        $dbHrPayrollDetails = $this->hrPayrollDetailsRepo->find($hrPayrollDetails->id);
        $this->assertModelData($fakeHrPayrollDetails, $dbHrPayrollDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_payroll_details()
    {
        $hrPayrollDetails = factory(HrPayrollDetails::class)->create();

        $resp = $this->hrPayrollDetailsRepo->delete($hrPayrollDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(HrPayrollDetails::find($hrPayrollDetails->id), 'HrPayrollDetails should not exist in DB');
    }
}
