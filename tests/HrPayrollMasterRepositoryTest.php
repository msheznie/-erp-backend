<?php namespace Tests\Repositories;

use App\Models\HrPayrollMaster;
use App\Repositories\HrPayrollMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrPayrollMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrPayrollMasterRepository
     */
    protected $hrPayrollMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrPayrollMasterRepo = \App::make(HrPayrollMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_payroll_master()
    {
        $hrPayrollMaster = factory(HrPayrollMaster::class)->make()->toArray();

        $createdHrPayrollMaster = $this->hrPayrollMasterRepo->create($hrPayrollMaster);

        $createdHrPayrollMaster = $createdHrPayrollMaster->toArray();
        $this->assertArrayHasKey('id', $createdHrPayrollMaster);
        $this->assertNotNull($createdHrPayrollMaster['id'], 'Created HrPayrollMaster must have id specified');
        $this->assertNotNull(HrPayrollMaster::find($createdHrPayrollMaster['id']), 'HrPayrollMaster with given id must be in DB');
        $this->assertModelData($hrPayrollMaster, $createdHrPayrollMaster);
    }

    /**
     * @test read
     */
    public function test_read_hr_payroll_master()
    {
        $hrPayrollMaster = factory(HrPayrollMaster::class)->create();

        $dbHrPayrollMaster = $this->hrPayrollMasterRepo->find($hrPayrollMaster->id);

        $dbHrPayrollMaster = $dbHrPayrollMaster->toArray();
        $this->assertModelData($hrPayrollMaster->toArray(), $dbHrPayrollMaster);
    }

    /**
     * @test update
     */
    public function test_update_hr_payroll_master()
    {
        $hrPayrollMaster = factory(HrPayrollMaster::class)->create();
        $fakeHrPayrollMaster = factory(HrPayrollMaster::class)->make()->toArray();

        $updatedHrPayrollMaster = $this->hrPayrollMasterRepo->update($fakeHrPayrollMaster, $hrPayrollMaster->id);

        $this->assertModelData($fakeHrPayrollMaster, $updatedHrPayrollMaster->toArray());
        $dbHrPayrollMaster = $this->hrPayrollMasterRepo->find($hrPayrollMaster->id);
        $this->assertModelData($fakeHrPayrollMaster, $dbHrPayrollMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_payroll_master()
    {
        $hrPayrollMaster = factory(HrPayrollMaster::class)->create();

        $resp = $this->hrPayrollMasterRepo->delete($hrPayrollMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(HrPayrollMaster::find($hrPayrollMaster->id), 'HrPayrollMaster should not exist in DB');
    }
}
