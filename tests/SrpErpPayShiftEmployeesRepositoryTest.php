<?php namespace Tests\Repositories;

use App\Models\SrpErpPayShiftEmployees;
use App\Repositories\SrpErpPayShiftEmployeesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrpErpPayShiftEmployeesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrpErpPayShiftEmployeesRepository
     */
    protected $srpErpPayShiftEmployeesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srpErpPayShiftEmployeesRepo = \App::make(SrpErpPayShiftEmployeesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srp_erp_pay_shift_employees()
    {
        $srpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->make()->toArray();

        $createdSrpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepo->create($srpErpPayShiftEmployees);

        $createdSrpErpPayShiftEmployees = $createdSrpErpPayShiftEmployees->toArray();
        $this->assertArrayHasKey('id', $createdSrpErpPayShiftEmployees);
        $this->assertNotNull($createdSrpErpPayShiftEmployees['id'], 'Created SrpErpPayShiftEmployees must have id specified');
        $this->assertNotNull(SrpErpPayShiftEmployees::find($createdSrpErpPayShiftEmployees['id']), 'SrpErpPayShiftEmployees with given id must be in DB');
        $this->assertModelData($srpErpPayShiftEmployees, $createdSrpErpPayShiftEmployees);
    }

    /**
     * @test read
     */
    public function test_read_srp_erp_pay_shift_employees()
    {
        $srpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->create();

        $dbSrpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepo->find($srpErpPayShiftEmployees->id);

        $dbSrpErpPayShiftEmployees = $dbSrpErpPayShiftEmployees->toArray();
        $this->assertModelData($srpErpPayShiftEmployees->toArray(), $dbSrpErpPayShiftEmployees);
    }

    /**
     * @test update
     */
    public function test_update_srp_erp_pay_shift_employees()
    {
        $srpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->create();
        $fakeSrpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->make()->toArray();

        $updatedSrpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepo->update($fakeSrpErpPayShiftEmployees, $srpErpPayShiftEmployees->id);

        $this->assertModelData($fakeSrpErpPayShiftEmployees, $updatedSrpErpPayShiftEmployees->toArray());
        $dbSrpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepo->find($srpErpPayShiftEmployees->id);
        $this->assertModelData($fakeSrpErpPayShiftEmployees, $dbSrpErpPayShiftEmployees->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srp_erp_pay_shift_employees()
    {
        $srpErpPayShiftEmployees = factory(SrpErpPayShiftEmployees::class)->create();

        $resp = $this->srpErpPayShiftEmployeesRepo->delete($srpErpPayShiftEmployees->id);

        $this->assertTrue($resp);
        $this->assertNull(SrpErpPayShiftEmployees::find($srpErpPayShiftEmployees->id), 'SrpErpPayShiftEmployees should not exist in DB');
    }
}
