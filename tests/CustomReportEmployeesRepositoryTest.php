<?php namespace Tests\Repositories;

use App\Models\CustomReportEmployees;
use App\Repositories\CustomReportEmployeesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomReportEmployeesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomReportEmployeesRepository
     */
    protected $customReportEmployeesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customReportEmployeesRepo = \App::make(CustomReportEmployeesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_custom_report_employees()
    {
        $customReportEmployees = factory(CustomReportEmployees::class)->make()->toArray();

        $createdCustomReportEmployees = $this->customReportEmployeesRepo->create($customReportEmployees);

        $createdCustomReportEmployees = $createdCustomReportEmployees->toArray();
        $this->assertArrayHasKey('id', $createdCustomReportEmployees);
        $this->assertNotNull($createdCustomReportEmployees['id'], 'Created CustomReportEmployees must have id specified');
        $this->assertNotNull(CustomReportEmployees::find($createdCustomReportEmployees['id']), 'CustomReportEmployees with given id must be in DB');
        $this->assertModelData($customReportEmployees, $createdCustomReportEmployees);
    }

    /**
     * @test read
     */
    public function test_read_custom_report_employees()
    {
        $customReportEmployees = factory(CustomReportEmployees::class)->create();

        $dbCustomReportEmployees = $this->customReportEmployeesRepo->find($customReportEmployees->id);

        $dbCustomReportEmployees = $dbCustomReportEmployees->toArray();
        $this->assertModelData($customReportEmployees->toArray(), $dbCustomReportEmployees);
    }

    /**
     * @test update
     */
    public function test_update_custom_report_employees()
    {
        $customReportEmployees = factory(CustomReportEmployees::class)->create();
        $fakeCustomReportEmployees = factory(CustomReportEmployees::class)->make()->toArray();

        $updatedCustomReportEmployees = $this->customReportEmployeesRepo->update($fakeCustomReportEmployees, $customReportEmployees->id);

        $this->assertModelData($fakeCustomReportEmployees, $updatedCustomReportEmployees->toArray());
        $dbCustomReportEmployees = $this->customReportEmployeesRepo->find($customReportEmployees->id);
        $this->assertModelData($fakeCustomReportEmployees, $dbCustomReportEmployees->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_custom_report_employees()
    {
        $customReportEmployees = factory(CustomReportEmployees::class)->create();

        $resp = $this->customReportEmployeesRepo->delete($customReportEmployees->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomReportEmployees::find($customReportEmployees->id), 'CustomReportEmployees should not exist in DB');
    }
}
