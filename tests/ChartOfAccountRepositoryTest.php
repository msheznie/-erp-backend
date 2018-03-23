<?php

use App\Models\ChartOfAccount;
use App\Repositories\ChartOfAccountRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChartOfAccountRepositoryTest extends TestCase
{
    use MakeChartOfAccountTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChartOfAccountRepository
     */
    protected $chartOfAccountRepo;

    public function setUp()
    {
        parent::setUp();
        $this->chartOfAccountRepo = App::make(ChartOfAccountRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateChartOfAccount()
    {
        $chartOfAccount = $this->fakeChartOfAccountData();
        $createdChartOfAccount = $this->chartOfAccountRepo->create($chartOfAccount);
        $createdChartOfAccount = $createdChartOfAccount->toArray();
        $this->assertArrayHasKey('id', $createdChartOfAccount);
        $this->assertNotNull($createdChartOfAccount['id'], 'Created ChartOfAccount must have id specified');
        $this->assertNotNull(ChartOfAccount::find($createdChartOfAccount['id']), 'ChartOfAccount with given id must be in DB');
        $this->assertModelData($chartOfAccount, $createdChartOfAccount);
    }

    /**
     * @test read
     */
    public function testReadChartOfAccount()
    {
        $chartOfAccount = $this->makeChartOfAccount();
        $dbChartOfAccount = $this->chartOfAccountRepo->find($chartOfAccount->id);
        $dbChartOfAccount = $dbChartOfAccount->toArray();
        $this->assertModelData($chartOfAccount->toArray(), $dbChartOfAccount);
    }

    /**
     * @test update
     */
    public function testUpdateChartOfAccount()
    {
        $chartOfAccount = $this->makeChartOfAccount();
        $fakeChartOfAccount = $this->fakeChartOfAccountData();
        $updatedChartOfAccount = $this->chartOfAccountRepo->update($fakeChartOfAccount, $chartOfAccount->id);
        $this->assertModelData($fakeChartOfAccount, $updatedChartOfAccount->toArray());
        $dbChartOfAccount = $this->chartOfAccountRepo->find($chartOfAccount->id);
        $this->assertModelData($fakeChartOfAccount, $dbChartOfAccount->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteChartOfAccount()
    {
        $chartOfAccount = $this->makeChartOfAccount();
        $resp = $this->chartOfAccountRepo->delete($chartOfAccount->id);
        $this->assertTrue($resp);
        $this->assertNull(ChartOfAccount::find($chartOfAccount->id), 'ChartOfAccount should not exist in DB');
    }
}
