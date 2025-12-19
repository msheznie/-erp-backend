<?php

use App\Models\ControlAccount;
use App\Repositories\ControlAccountRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ControlAccountRepositoryTest extends TestCase
{
    use MakeControlAccountTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ControlAccountRepository
     */
    protected $controlAccountRepo;

    public function setUp()
    {
        parent::setUp();
        $this->controlAccountRepo = App::make(ControlAccountRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateControlAccount()
    {
        $controlAccount = $this->fakeControlAccountData();
        $createdControlAccount = $this->controlAccountRepo->create($controlAccount);
        $createdControlAccount = $createdControlAccount->toArray();
        $this->assertArrayHasKey('id', $createdControlAccount);
        $this->assertNotNull($createdControlAccount['id'], 'Created ControlAccount must have id specified');
        $this->assertNotNull(ControlAccount::find($createdControlAccount['id']), 'ControlAccount with given id must be in DB');
        $this->assertModelData($controlAccount, $createdControlAccount);
    }

    /**
     * @test read
     */
    public function testReadControlAccount()
    {
        $controlAccount = $this->makeControlAccount();
        $dbControlAccount = $this->controlAccountRepo->find($controlAccount->id);
        $dbControlAccount = $dbControlAccount->toArray();
        $this->assertModelData($controlAccount->toArray(), $dbControlAccount);
    }

    /**
     * @test update
     */
    public function testUpdateControlAccount()
    {
        $controlAccount = $this->makeControlAccount();
        $fakeControlAccount = $this->fakeControlAccountData();
        $updatedControlAccount = $this->controlAccountRepo->update($fakeControlAccount, $controlAccount->id);
        $this->assertModelData($fakeControlAccount, $updatedControlAccount->toArray());
        $dbControlAccount = $this->controlAccountRepo->find($controlAccount->id);
        $this->assertModelData($fakeControlAccount, $dbControlAccount->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteControlAccount()
    {
        $controlAccount = $this->makeControlAccount();
        $resp = $this->controlAccountRepo->delete($controlAccount->id);
        $this->assertTrue($resp);
        $this->assertNull(ControlAccount::find($controlAccount->id), 'ControlAccount should not exist in DB');
    }
}
