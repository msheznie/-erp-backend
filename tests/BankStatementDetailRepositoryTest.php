<?php namespace Tests\Repositories;

use App\Models\BankStatementDetail;
use App\Repositories\BankStatementDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BankStatementDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankStatementDetailRepository
     */
    protected $bankStatementDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bankStatementDetailRepo = \App::make(BankStatementDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bank_statement_detail()
    {
        $bankStatementDetail = factory(BankStatementDetail::class)->make()->toArray();

        $createdBankStatementDetail = $this->bankStatementDetailRepo->create($bankStatementDetail);

        $createdBankStatementDetail = $createdBankStatementDetail->toArray();
        $this->assertArrayHasKey('id', $createdBankStatementDetail);
        $this->assertNotNull($createdBankStatementDetail['id'], 'Created BankStatementDetail must have id specified');
        $this->assertNotNull(BankStatementDetail::find($createdBankStatementDetail['id']), 'BankStatementDetail with given id must be in DB');
        $this->assertModelData($bankStatementDetail, $createdBankStatementDetail);
    }

    /**
     * @test read
     */
    public function test_read_bank_statement_detail()
    {
        $bankStatementDetail = factory(BankStatementDetail::class)->create();

        $dbBankStatementDetail = $this->bankStatementDetailRepo->find($bankStatementDetail->id);

        $dbBankStatementDetail = $dbBankStatementDetail->toArray();
        $this->assertModelData($bankStatementDetail->toArray(), $dbBankStatementDetail);
    }

    /**
     * @test update
     */
    public function test_update_bank_statement_detail()
    {
        $bankStatementDetail = factory(BankStatementDetail::class)->create();
        $fakeBankStatementDetail = factory(BankStatementDetail::class)->make()->toArray();

        $updatedBankStatementDetail = $this->bankStatementDetailRepo->update($fakeBankStatementDetail, $bankStatementDetail->id);

        $this->assertModelData($fakeBankStatementDetail, $updatedBankStatementDetail->toArray());
        $dbBankStatementDetail = $this->bankStatementDetailRepo->find($bankStatementDetail->id);
        $this->assertModelData($fakeBankStatementDetail, $dbBankStatementDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bank_statement_detail()
    {
        $bankStatementDetail = factory(BankStatementDetail::class)->create();

        $resp = $this->bankStatementDetailRepo->delete($bankStatementDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(BankStatementDetail::find($bankStatementDetail->id), 'BankStatementDetail should not exist in DB');
    }
}
