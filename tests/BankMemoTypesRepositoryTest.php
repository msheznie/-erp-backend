<?php

use App\Models\BankMemoTypes;
use App\Repositories\BankMemoTypesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMemoTypesRepositoryTest extends TestCase
{
    use MakeBankMemoTypesTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankMemoTypesRepository
     */
    protected $bankMemoTypesRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankMemoTypesRepo = App::make(BankMemoTypesRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankMemoTypes()
    {
        $bankMemoTypes = $this->fakeBankMemoTypesData();
        $createdBankMemoTypes = $this->bankMemoTypesRepo->create($bankMemoTypes);
        $createdBankMemoTypes = $createdBankMemoTypes->toArray();
        $this->assertArrayHasKey('id', $createdBankMemoTypes);
        $this->assertNotNull($createdBankMemoTypes['id'], 'Created BankMemoTypes must have id specified');
        $this->assertNotNull(BankMemoTypes::find($createdBankMemoTypes['id']), 'BankMemoTypes with given id must be in DB');
        $this->assertModelData($bankMemoTypes, $createdBankMemoTypes);
    }

    /**
     * @test read
     */
    public function testReadBankMemoTypes()
    {
        $bankMemoTypes = $this->makeBankMemoTypes();
        $dbBankMemoTypes = $this->bankMemoTypesRepo->find($bankMemoTypes->id);
        $dbBankMemoTypes = $dbBankMemoTypes->toArray();
        $this->assertModelData($bankMemoTypes->toArray(), $dbBankMemoTypes);
    }

    /**
     * @test update
     */
    public function testUpdateBankMemoTypes()
    {
        $bankMemoTypes = $this->makeBankMemoTypes();
        $fakeBankMemoTypes = $this->fakeBankMemoTypesData();
        $updatedBankMemoTypes = $this->bankMemoTypesRepo->update($fakeBankMemoTypes, $bankMemoTypes->id);
        $this->assertModelData($fakeBankMemoTypes, $updatedBankMemoTypes->toArray());
        $dbBankMemoTypes = $this->bankMemoTypesRepo->find($bankMemoTypes->id);
        $this->assertModelData($fakeBankMemoTypes, $dbBankMemoTypes->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankMemoTypes()
    {
        $bankMemoTypes = $this->makeBankMemoTypes();
        $resp = $this->bankMemoTypesRepo->delete($bankMemoTypes->id);
        $this->assertTrue($resp);
        $this->assertNull(BankMemoTypes::find($bankMemoTypes->id), 'BankMemoTypes should not exist in DB');
    }
}
