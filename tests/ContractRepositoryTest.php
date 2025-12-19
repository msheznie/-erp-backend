<?php

use App\Models\Contract;
use App\Repositories\ContractRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContractRepositoryTest extends TestCase
{
    use MakeContractTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ContractRepository
     */
    protected $contractRepo;

    public function setUp()
    {
        parent::setUp();
        $this->contractRepo = App::make(ContractRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateContract()
    {
        $contract = $this->fakeContractData();
        $createdContract = $this->contractRepo->create($contract);
        $createdContract = $createdContract->toArray();
        $this->assertArrayHasKey('id', $createdContract);
        $this->assertNotNull($createdContract['id'], 'Created Contract must have id specified');
        $this->assertNotNull(Contract::find($createdContract['id']), 'Contract with given id must be in DB');
        $this->assertModelData($contract, $createdContract);
    }

    /**
     * @test read
     */
    public function testReadContract()
    {
        $contract = $this->makeContract();
        $dbContract = $this->contractRepo->find($contract->id);
        $dbContract = $dbContract->toArray();
        $this->assertModelData($contract->toArray(), $dbContract);
    }

    /**
     * @test update
     */
    public function testUpdateContract()
    {
        $contract = $this->makeContract();
        $fakeContract = $this->fakeContractData();
        $updatedContract = $this->contractRepo->update($fakeContract, $contract->id);
        $this->assertModelData($fakeContract, $updatedContract->toArray());
        $dbContract = $this->contractRepo->find($contract->id);
        $this->assertModelData($fakeContract, $dbContract->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteContract()
    {
        $contract = $this->makeContract();
        $resp = $this->contractRepo->delete($contract->id);
        $this->assertTrue($resp);
        $this->assertNull(Contract::find($contract->id), 'Contract should not exist in DB');
    }
}
