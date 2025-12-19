<?php

use App\Models\BankMemoSupplierMaster;
use App\Repositories\BankMemoSupplierMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMemoSupplierMasterRepositoryTest extends TestCase
{
    use MakeBankMemoSupplierMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankMemoSupplierMasterRepository
     */
    protected $bankMemoSupplierMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankMemoSupplierMasterRepo = App::make(BankMemoSupplierMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankMemoSupplierMaster()
    {
        $bankMemoSupplierMaster = $this->fakeBankMemoSupplierMasterData();
        $createdBankMemoSupplierMaster = $this->bankMemoSupplierMasterRepo->create($bankMemoSupplierMaster);
        $createdBankMemoSupplierMaster = $createdBankMemoSupplierMaster->toArray();
        $this->assertArrayHasKey('id', $createdBankMemoSupplierMaster);
        $this->assertNotNull($createdBankMemoSupplierMaster['id'], 'Created BankMemoSupplierMaster must have id specified');
        $this->assertNotNull(BankMemoSupplierMaster::find($createdBankMemoSupplierMaster['id']), 'BankMemoSupplierMaster with given id must be in DB');
        $this->assertModelData($bankMemoSupplierMaster, $createdBankMemoSupplierMaster);
    }

    /**
     * @test read
     */
    public function testReadBankMemoSupplierMaster()
    {
        $bankMemoSupplierMaster = $this->makeBankMemoSupplierMaster();
        $dbBankMemoSupplierMaster = $this->bankMemoSupplierMasterRepo->find($bankMemoSupplierMaster->id);
        $dbBankMemoSupplierMaster = $dbBankMemoSupplierMaster->toArray();
        $this->assertModelData($bankMemoSupplierMaster->toArray(), $dbBankMemoSupplierMaster);
    }

    /**
     * @test update
     */
    public function testUpdateBankMemoSupplierMaster()
    {
        $bankMemoSupplierMaster = $this->makeBankMemoSupplierMaster();
        $fakeBankMemoSupplierMaster = $this->fakeBankMemoSupplierMasterData();
        $updatedBankMemoSupplierMaster = $this->bankMemoSupplierMasterRepo->update($fakeBankMemoSupplierMaster, $bankMemoSupplierMaster->id);
        $this->assertModelData($fakeBankMemoSupplierMaster, $updatedBankMemoSupplierMaster->toArray());
        $dbBankMemoSupplierMaster = $this->bankMemoSupplierMasterRepo->find($bankMemoSupplierMaster->id);
        $this->assertModelData($fakeBankMemoSupplierMaster, $dbBankMemoSupplierMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankMemoSupplierMaster()
    {
        $bankMemoSupplierMaster = $this->makeBankMemoSupplierMaster();
        $resp = $this->bankMemoSupplierMasterRepo->delete($bankMemoSupplierMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(BankMemoSupplierMaster::find($bankMemoSupplierMaster->id), 'BankMemoSupplierMaster should not exist in DB');
    }
}
