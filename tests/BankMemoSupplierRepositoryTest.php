<?php

use App\Models\BankMemoSupplier;
use App\Repositories\BankMemoSupplierRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMemoSupplierRepositoryTest extends TestCase
{
    use MakeBankMemoSupplierTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankMemoSupplierRepository
     */
    protected $bankMemoSupplierRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankMemoSupplierRepo = App::make(BankMemoSupplierRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankMemoSupplier()
    {
        $bankMemoSupplier = $this->fakeBankMemoSupplierData();
        $createdBankMemoSupplier = $this->bankMemoSupplierRepo->create($bankMemoSupplier);
        $createdBankMemoSupplier = $createdBankMemoSupplier->toArray();
        $this->assertArrayHasKey('id', $createdBankMemoSupplier);
        $this->assertNotNull($createdBankMemoSupplier['id'], 'Created BankMemoSupplier must have id specified');
        $this->assertNotNull(BankMemoSupplier::find($createdBankMemoSupplier['id']), 'BankMemoSupplier with given id must be in DB');
        $this->assertModelData($bankMemoSupplier, $createdBankMemoSupplier);
    }

    /**
     * @test read
     */
    public function testReadBankMemoSupplier()
    {
        $bankMemoSupplier = $this->makeBankMemoSupplier();
        $dbBankMemoSupplier = $this->bankMemoSupplierRepo->find($bankMemoSupplier->id);
        $dbBankMemoSupplier = $dbBankMemoSupplier->toArray();
        $this->assertModelData($bankMemoSupplier->toArray(), $dbBankMemoSupplier);
    }

    /**
     * @test update
     */
    public function testUpdateBankMemoSupplier()
    {
        $bankMemoSupplier = $this->makeBankMemoSupplier();
        $fakeBankMemoSupplier = $this->fakeBankMemoSupplierData();
        $updatedBankMemoSupplier = $this->bankMemoSupplierRepo->update($fakeBankMemoSupplier, $bankMemoSupplier->id);
        $this->assertModelData($fakeBankMemoSupplier, $updatedBankMemoSupplier->toArray());
        $dbBankMemoSupplier = $this->bankMemoSupplierRepo->find($bankMemoSupplier->id);
        $this->assertModelData($fakeBankMemoSupplier, $dbBankMemoSupplier->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankMemoSupplier()
    {
        $bankMemoSupplier = $this->makeBankMemoSupplier();
        $resp = $this->bankMemoSupplierRepo->delete($bankMemoSupplier->id);
        $this->assertTrue($resp);
        $this->assertNull(BankMemoSupplier::find($bankMemoSupplier->id), 'BankMemoSupplier should not exist in DB');
    }
}
