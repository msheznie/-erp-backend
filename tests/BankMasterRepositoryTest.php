<?php

use App\Models\BankMaster;
use App\Repositories\BankMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMasterRepositoryTest extends TestCase
{
    use MakeBankMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankMasterRepository
     */
    protected $bankMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankMasterRepo = App::make(BankMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankMaster()
    {
        $bankMaster = $this->fakeBankMasterData();
        $createdBankMaster = $this->bankMasterRepo->create($bankMaster);
        $createdBankMaster = $createdBankMaster->toArray();
        $this->assertArrayHasKey('id', $createdBankMaster);
        $this->assertNotNull($createdBankMaster['id'], 'Created BankMaster must have id specified');
        $this->assertNotNull(BankMaster::find($createdBankMaster['id']), 'BankMaster with given id must be in DB');
        $this->assertModelData($bankMaster, $createdBankMaster);
    }

    /**
     * @test read
     */
    public function testReadBankMaster()
    {
        $bankMaster = $this->makeBankMaster();
        $dbBankMaster = $this->bankMasterRepo->find($bankMaster->id);
        $dbBankMaster = $dbBankMaster->toArray();
        $this->assertModelData($bankMaster->toArray(), $dbBankMaster);
    }

    /**
     * @test update
     */
    public function testUpdateBankMaster()
    {
        $bankMaster = $this->makeBankMaster();
        $fakeBankMaster = $this->fakeBankMasterData();
        $updatedBankMaster = $this->bankMasterRepo->update($fakeBankMaster, $bankMaster->id);
        $this->assertModelData($fakeBankMaster, $updatedBankMaster->toArray());
        $dbBankMaster = $this->bankMasterRepo->find($bankMaster->id);
        $this->assertModelData($fakeBankMaster, $dbBankMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankMaster()
    {
        $bankMaster = $this->makeBankMaster();
        $resp = $this->bankMasterRepo->delete($bankMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(BankMaster::find($bankMaster->id), 'BankMaster should not exist in DB');
    }
}
