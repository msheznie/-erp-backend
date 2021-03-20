<?php namespace Tests\Repositories;

use App\Models\SMEEmpContractType;
use App\Repositories\SMEEmpContractTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMEEmpContractTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMEEmpContractTypeRepository
     */
    protected $sMEEmpContractTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMEEmpContractTypeRepo = \App::make(SMEEmpContractTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_emp_contract_type()
    {
        $sMEEmpContractType = factory(SMEEmpContractType::class)->make()->toArray();

        $createdSMEEmpContractType = $this->sMEEmpContractTypeRepo->create($sMEEmpContractType);

        $createdSMEEmpContractType = $createdSMEEmpContractType->toArray();
        $this->assertArrayHasKey('id', $createdSMEEmpContractType);
        $this->assertNotNull($createdSMEEmpContractType['id'], 'Created SMEEmpContractType must have id specified');
        $this->assertNotNull(SMEEmpContractType::find($createdSMEEmpContractType['id']), 'SMEEmpContractType with given id must be in DB');
        $this->assertModelData($sMEEmpContractType, $createdSMEEmpContractType);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_emp_contract_type()
    {
        $sMEEmpContractType = factory(SMEEmpContractType::class)->create();

        $dbSMEEmpContractType = $this->sMEEmpContractTypeRepo->find($sMEEmpContractType->id);

        $dbSMEEmpContractType = $dbSMEEmpContractType->toArray();
        $this->assertModelData($sMEEmpContractType->toArray(), $dbSMEEmpContractType);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_emp_contract_type()
    {
        $sMEEmpContractType = factory(SMEEmpContractType::class)->create();
        $fakeSMEEmpContractType = factory(SMEEmpContractType::class)->make()->toArray();

        $updatedSMEEmpContractType = $this->sMEEmpContractTypeRepo->update($fakeSMEEmpContractType, $sMEEmpContractType->id);

        $this->assertModelData($fakeSMEEmpContractType, $updatedSMEEmpContractType->toArray());
        $dbSMEEmpContractType = $this->sMEEmpContractTypeRepo->find($sMEEmpContractType->id);
        $this->assertModelData($fakeSMEEmpContractType, $dbSMEEmpContractType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_emp_contract_type()
    {
        $sMEEmpContractType = factory(SMEEmpContractType::class)->create();

        $resp = $this->sMEEmpContractTypeRepo->delete($sMEEmpContractType->id);

        $this->assertTrue($resp);
        $this->assertNull(SMEEmpContractType::find($sMEEmpContractType->id), 'SMEEmpContractType should not exist in DB');
    }
}
