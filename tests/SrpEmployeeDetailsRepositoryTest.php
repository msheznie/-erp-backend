<?php namespace Tests\Repositories;

use App\Models\SrpEmployeeDetails;
use App\Repositories\SrpEmployeeDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrpEmployeeDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrpEmployeeDetailsRepository
     */
    protected $srpEmployeeDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srpEmployeeDetailsRepo = \App::make(SrpEmployeeDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srp_employee_details()
    {
        $srpEmployeeDetails = factory(SrpEmployeeDetails::class)->make()->toArray();

        $createdSrpEmployeeDetails = $this->srpEmployeeDetailsRepo->create($srpEmployeeDetails);

        $createdSrpEmployeeDetails = $createdSrpEmployeeDetails->toArray();
        $this->assertArrayHasKey('id', $createdSrpEmployeeDetails);
        $this->assertNotNull($createdSrpEmployeeDetails['id'], 'Created SrpEmployeeDetails must have id specified');
        $this->assertNotNull(SrpEmployeeDetails::find($createdSrpEmployeeDetails['id']), 'SrpEmployeeDetails with given id must be in DB');
        $this->assertModelData($srpEmployeeDetails, $createdSrpEmployeeDetails);
    }

    /**
     * @test read
     */
    public function test_read_srp_employee_details()
    {
        $srpEmployeeDetails = factory(SrpEmployeeDetails::class)->create();

        $dbSrpEmployeeDetails = $this->srpEmployeeDetailsRepo->find($srpEmployeeDetails->id);

        $dbSrpEmployeeDetails = $dbSrpEmployeeDetails->toArray();
        $this->assertModelData($srpEmployeeDetails->toArray(), $dbSrpEmployeeDetails);
    }

    /**
     * @test update
     */
    public function test_update_srp_employee_details()
    {
        $srpEmployeeDetails = factory(SrpEmployeeDetails::class)->create();
        $fakeSrpEmployeeDetails = factory(SrpEmployeeDetails::class)->make()->toArray();

        $updatedSrpEmployeeDetails = $this->srpEmployeeDetailsRepo->update($fakeSrpEmployeeDetails, $srpEmployeeDetails->id);

        $this->assertModelData($fakeSrpEmployeeDetails, $updatedSrpEmployeeDetails->toArray());
        $dbSrpEmployeeDetails = $this->srpEmployeeDetailsRepo->find($srpEmployeeDetails->id);
        $this->assertModelData($fakeSrpEmployeeDetails, $dbSrpEmployeeDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srp_employee_details()
    {
        $srpEmployeeDetails = factory(SrpEmployeeDetails::class)->create();

        $resp = $this->srpEmployeeDetailsRepo->delete($srpEmployeeDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(SrpEmployeeDetails::find($srpEmployeeDetails->id), 'SrpEmployeeDetails should not exist in DB');
    }
}
