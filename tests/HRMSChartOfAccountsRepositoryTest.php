<?php

use App\Models\HRMSChartOfAccounts;
use App\Repositories\HRMSChartOfAccountsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HRMSChartOfAccountsRepositoryTest extends TestCase
{
    use MakeHRMSChartOfAccountsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSChartOfAccountsRepository
     */
    protected $hRMSChartOfAccountsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->hRMSChartOfAccountsRepo = App::make(HRMSChartOfAccountsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateHRMSChartOfAccounts()
    {
        $hRMSChartOfAccounts = $this->fakeHRMSChartOfAccountsData();
        $createdHRMSChartOfAccounts = $this->hRMSChartOfAccountsRepo->create($hRMSChartOfAccounts);
        $createdHRMSChartOfAccounts = $createdHRMSChartOfAccounts->toArray();
        $this->assertArrayHasKey('id', $createdHRMSChartOfAccounts);
        $this->assertNotNull($createdHRMSChartOfAccounts['id'], 'Created HRMSChartOfAccounts must have id specified');
        $this->assertNotNull(HRMSChartOfAccounts::find($createdHRMSChartOfAccounts['id']), 'HRMSChartOfAccounts with given id must be in DB');
        $this->assertModelData($hRMSChartOfAccounts, $createdHRMSChartOfAccounts);
    }

    /**
     * @test read
     */
    public function testReadHRMSChartOfAccounts()
    {
        $hRMSChartOfAccounts = $this->makeHRMSChartOfAccounts();
        $dbHRMSChartOfAccounts = $this->hRMSChartOfAccountsRepo->find($hRMSChartOfAccounts->id);
        $dbHRMSChartOfAccounts = $dbHRMSChartOfAccounts->toArray();
        $this->assertModelData($hRMSChartOfAccounts->toArray(), $dbHRMSChartOfAccounts);
    }

    /**
     * @test update
     */
    public function testUpdateHRMSChartOfAccounts()
    {
        $hRMSChartOfAccounts = $this->makeHRMSChartOfAccounts();
        $fakeHRMSChartOfAccounts = $this->fakeHRMSChartOfAccountsData();
        $updatedHRMSChartOfAccounts = $this->hRMSChartOfAccountsRepo->update($fakeHRMSChartOfAccounts, $hRMSChartOfAccounts->id);
        $this->assertModelData($fakeHRMSChartOfAccounts, $updatedHRMSChartOfAccounts->toArray());
        $dbHRMSChartOfAccounts = $this->hRMSChartOfAccountsRepo->find($hRMSChartOfAccounts->id);
        $this->assertModelData($fakeHRMSChartOfAccounts, $dbHRMSChartOfAccounts->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteHRMSChartOfAccounts()
    {
        $hRMSChartOfAccounts = $this->makeHRMSChartOfAccounts();
        $resp = $this->hRMSChartOfAccountsRepo->delete($hRMSChartOfAccounts->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSChartOfAccounts::find($hRMSChartOfAccounts->id), 'HRMSChartOfAccounts should not exist in DB');
    }
}
