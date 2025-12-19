<?php namespace Tests\Repositories;

use App\Models\SMESystemEmployeeType;
use App\Repositories\SMESystemEmployeeTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMESystemEmployeeTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMESystemEmployeeTypeRepository
     */
    protected $sMESystemEmployeeTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMESystemEmployeeTypeRepo = \App::make(SMESystemEmployeeTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_system_employee_type()
    {
        $sMESystemEmployeeType = factory(SMESystemEmployeeType::class)->make()->toArray();

        $createdSMESystemEmployeeType = $this->sMESystemEmployeeTypeRepo->create($sMESystemEmployeeType);

        $createdSMESystemEmployeeType = $createdSMESystemEmployeeType->toArray();
        $this->assertArrayHasKey('id', $createdSMESystemEmployeeType);
        $this->assertNotNull($createdSMESystemEmployeeType['id'], 'Created SMESystemEmployeeType must have id specified');
        $this->assertNotNull(SMESystemEmployeeType::find($createdSMESystemEmployeeType['id']), 'SMESystemEmployeeType with given id must be in DB');
        $this->assertModelData($sMESystemEmployeeType, $createdSMESystemEmployeeType);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_system_employee_type()
    {
        $sMESystemEmployeeType = factory(SMESystemEmployeeType::class)->create();

        $dbSMESystemEmployeeType = $this->sMESystemEmployeeTypeRepo->find($sMESystemEmployeeType->id);

        $dbSMESystemEmployeeType = $dbSMESystemEmployeeType->toArray();
        $this->assertModelData($sMESystemEmployeeType->toArray(), $dbSMESystemEmployeeType);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_system_employee_type()
    {
        $sMESystemEmployeeType = factory(SMESystemEmployeeType::class)->create();
        $fakeSMESystemEmployeeType = factory(SMESystemEmployeeType::class)->make()->toArray();

        $updatedSMESystemEmployeeType = $this->sMESystemEmployeeTypeRepo->update($fakeSMESystemEmployeeType, $sMESystemEmployeeType->id);

        $this->assertModelData($fakeSMESystemEmployeeType, $updatedSMESystemEmployeeType->toArray());
        $dbSMESystemEmployeeType = $this->sMESystemEmployeeTypeRepo->find($sMESystemEmployeeType->id);
        $this->assertModelData($fakeSMESystemEmployeeType, $dbSMESystemEmployeeType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_system_employee_type()
    {
        $sMESystemEmployeeType = factory(SMESystemEmployeeType::class)->create();

        $resp = $this->sMESystemEmployeeTypeRepo->delete($sMESystemEmployeeType->id);

        $this->assertTrue($resp);
        $this->assertNull(SMESystemEmployeeType::find($sMESystemEmployeeType->id), 'SMESystemEmployeeType should not exist in DB');
    }
}
