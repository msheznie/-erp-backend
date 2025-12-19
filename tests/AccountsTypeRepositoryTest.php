<?php

use App\Models\AccountsType;
use App\Repositories\AccountsTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountsTypeRepositoryTest extends TestCase
{
    use MakeAccountsTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccountsTypeRepository
     */
    protected $accountsTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->accountsTypeRepo = App::make(AccountsTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAccountsType()
    {
        $accountsType = $this->fakeAccountsTypeData();
        $createdAccountsType = $this->accountsTypeRepo->create($accountsType);
        $createdAccountsType = $createdAccountsType->toArray();
        $this->assertArrayHasKey('id', $createdAccountsType);
        $this->assertNotNull($createdAccountsType['id'], 'Created AccountsType must have id specified');
        $this->assertNotNull(AccountsType::find($createdAccountsType['id']), 'AccountsType with given id must be in DB');
        $this->assertModelData($accountsType, $createdAccountsType);
    }

    /**
     * @test read
     */
    public function testReadAccountsType()
    {
        $accountsType = $this->makeAccountsType();
        $dbAccountsType = $this->accountsTypeRepo->find($accountsType->id);
        $dbAccountsType = $dbAccountsType->toArray();
        $this->assertModelData($accountsType->toArray(), $dbAccountsType);
    }

    /**
     * @test update
     */
    public function testUpdateAccountsType()
    {
        $accountsType = $this->makeAccountsType();
        $fakeAccountsType = $this->fakeAccountsTypeData();
        $updatedAccountsType = $this->accountsTypeRepo->update($fakeAccountsType, $accountsType->id);
        $this->assertModelData($fakeAccountsType, $updatedAccountsType->toArray());
        $dbAccountsType = $this->accountsTypeRepo->find($accountsType->id);
        $this->assertModelData($fakeAccountsType, $dbAccountsType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAccountsType()
    {
        $accountsType = $this->makeAccountsType();
        $resp = $this->accountsTypeRepo->delete($accountsType->id);
        $this->assertTrue($resp);
        $this->assertNull(AccountsType::find($accountsType->id), 'AccountsType should not exist in DB');
    }
}
