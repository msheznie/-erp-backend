<?php

use App\Models\BankAssign;
use App\Repositories\BankAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankAssignRepositoryTest extends TestCase
{
    use MakeBankAssignTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankAssignRepository
     */
    protected $bankAssignRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankAssignRepo = App::make(BankAssignRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankAssign()
    {
        $bankAssign = $this->fakeBankAssignData();
        $createdBankAssign = $this->bankAssignRepo->create($bankAssign);
        $createdBankAssign = $createdBankAssign->toArray();
        $this->assertArrayHasKey('id', $createdBankAssign);
        $this->assertNotNull($createdBankAssign['id'], 'Created BankAssign must have id specified');
        $this->assertNotNull(BankAssign::find($createdBankAssign['id']), 'BankAssign with given id must be in DB');
        $this->assertModelData($bankAssign, $createdBankAssign);
    }

    /**
     * @test read
     */
    public function testReadBankAssign()
    {
        $bankAssign = $this->makeBankAssign();
        $dbBankAssign = $this->bankAssignRepo->find($bankAssign->id);
        $dbBankAssign = $dbBankAssign->toArray();
        $this->assertModelData($bankAssign->toArray(), $dbBankAssign);
    }

    /**
     * @test update
     */
    public function testUpdateBankAssign()
    {
        $bankAssign = $this->makeBankAssign();
        $fakeBankAssign = $this->fakeBankAssignData();
        $updatedBankAssign = $this->bankAssignRepo->update($fakeBankAssign, $bankAssign->id);
        $this->assertModelData($fakeBankAssign, $updatedBankAssign->toArray());
        $dbBankAssign = $this->bankAssignRepo->find($bankAssign->id);
        $this->assertModelData($fakeBankAssign, $dbBankAssign->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankAssign()
    {
        $bankAssign = $this->makeBankAssign();
        $resp = $this->bankAssignRepo->delete($bankAssign->id);
        $this->assertTrue($resp);
        $this->assertNull(BankAssign::find($bankAssign->id), 'BankAssign should not exist in DB');
    }
}
