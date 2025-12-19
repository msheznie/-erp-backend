<?php namespace Tests\Repositories;

use App\Models\BankStatementMaster;
use App\Repositories\BankStatementMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BankStatementMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankStatementMasterRepository
     */
    protected $bankStatementMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bankStatementMasterRepo = \App::make(BankStatementMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bank_statement_master()
    {
        $bankStatementMaster = factory(BankStatementMaster::class)->make()->toArray();

        $createdBankStatementMaster = $this->bankStatementMasterRepo->create($bankStatementMaster);

        $createdBankStatementMaster = $createdBankStatementMaster->toArray();
        $this->assertArrayHasKey('id', $createdBankStatementMaster);
        $this->assertNotNull($createdBankStatementMaster['id'], 'Created BankStatementMaster must have id specified');
        $this->assertNotNull(BankStatementMaster::find($createdBankStatementMaster['id']), 'BankStatementMaster with given id must be in DB');
        $this->assertModelData($bankStatementMaster, $createdBankStatementMaster);
    }

    /**
     * @test read
     */
    public function test_read_bank_statement_master()
    {
        $bankStatementMaster = factory(BankStatementMaster::class)->create();

        $dbBankStatementMaster = $this->bankStatementMasterRepo->find($bankStatementMaster->id);

        $dbBankStatementMaster = $dbBankStatementMaster->toArray();
        $this->assertModelData($bankStatementMaster->toArray(), $dbBankStatementMaster);
    }

    /**
     * @test update
     */
    public function test_update_bank_statement_master()
    {
        $bankStatementMaster = factory(BankStatementMaster::class)->create();
        $fakeBankStatementMaster = factory(BankStatementMaster::class)->make()->toArray();

        $updatedBankStatementMaster = $this->bankStatementMasterRepo->update($fakeBankStatementMaster, $bankStatementMaster->id);

        $this->assertModelData($fakeBankStatementMaster, $updatedBankStatementMaster->toArray());
        $dbBankStatementMaster = $this->bankStatementMasterRepo->find($bankStatementMaster->id);
        $this->assertModelData($fakeBankStatementMaster, $dbBankStatementMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bank_statement_master()
    {
        $bankStatementMaster = factory(BankStatementMaster::class)->create();

        $resp = $this->bankStatementMasterRepo->delete($bankStatementMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(BankStatementMaster::find($bankStatementMaster->id), 'BankStatementMaster should not exist in DB');
    }
}
