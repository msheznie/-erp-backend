<?php

use App\Models\BankMemoPayee;
use App\Repositories\BankMemoPayeeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMemoPayeeRepositoryTest extends TestCase
{
    use MakeBankMemoPayeeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankMemoPayeeRepository
     */
    protected $bankMemoPayeeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankMemoPayeeRepo = App::make(BankMemoPayeeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankMemoPayee()
    {
        $bankMemoPayee = $this->fakeBankMemoPayeeData();
        $createdBankMemoPayee = $this->bankMemoPayeeRepo->create($bankMemoPayee);
        $createdBankMemoPayee = $createdBankMemoPayee->toArray();
        $this->assertArrayHasKey('id', $createdBankMemoPayee);
        $this->assertNotNull($createdBankMemoPayee['id'], 'Created BankMemoPayee must have id specified');
        $this->assertNotNull(BankMemoPayee::find($createdBankMemoPayee['id']), 'BankMemoPayee with given id must be in DB');
        $this->assertModelData($bankMemoPayee, $createdBankMemoPayee);
    }

    /**
     * @test read
     */
    public function testReadBankMemoPayee()
    {
        $bankMemoPayee = $this->makeBankMemoPayee();
        $dbBankMemoPayee = $this->bankMemoPayeeRepo->find($bankMemoPayee->id);
        $dbBankMemoPayee = $dbBankMemoPayee->toArray();
        $this->assertModelData($bankMemoPayee->toArray(), $dbBankMemoPayee);
    }

    /**
     * @test update
     */
    public function testUpdateBankMemoPayee()
    {
        $bankMemoPayee = $this->makeBankMemoPayee();
        $fakeBankMemoPayee = $this->fakeBankMemoPayeeData();
        $updatedBankMemoPayee = $this->bankMemoPayeeRepo->update($fakeBankMemoPayee, $bankMemoPayee->id);
        $this->assertModelData($fakeBankMemoPayee, $updatedBankMemoPayee->toArray());
        $dbBankMemoPayee = $this->bankMemoPayeeRepo->find($bankMemoPayee->id);
        $this->assertModelData($fakeBankMemoPayee, $dbBankMemoPayee->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankMemoPayee()
    {
        $bankMemoPayee = $this->makeBankMemoPayee();
        $resp = $this->bankMemoPayeeRepo->delete($bankMemoPayee->id);
        $this->assertTrue($resp);
        $this->assertNull(BankMemoPayee::find($bankMemoPayee->id), 'BankMemoPayee should not exist in DB');
    }
}
