<?php

use App\Models\ChartOfAccountsRefferedBack;
use App\Repositories\ChartOfAccountsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChartOfAccountsRefferedBackRepositoryTest extends TestCase
{
    use MakeChartOfAccountsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChartOfAccountsRefferedBackRepository
     */
    protected $chartOfAccountsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->chartOfAccountsRefferedBackRepo = App::make(ChartOfAccountsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateChartOfAccountsRefferedBack()
    {
        $chartOfAccountsRefferedBack = $this->fakeChartOfAccountsRefferedBackData();
        $createdChartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepo->create($chartOfAccountsRefferedBack);
        $createdChartOfAccountsRefferedBack = $createdChartOfAccountsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdChartOfAccountsRefferedBack);
        $this->assertNotNull($createdChartOfAccountsRefferedBack['id'], 'Created ChartOfAccountsRefferedBack must have id specified');
        $this->assertNotNull(ChartOfAccountsRefferedBack::find($createdChartOfAccountsRefferedBack['id']), 'ChartOfAccountsRefferedBack with given id must be in DB');
        $this->assertModelData($chartOfAccountsRefferedBack, $createdChartOfAccountsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadChartOfAccountsRefferedBack()
    {
        $chartOfAccountsRefferedBack = $this->makeChartOfAccountsRefferedBack();
        $dbChartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepo->find($chartOfAccountsRefferedBack->id);
        $dbChartOfAccountsRefferedBack = $dbChartOfAccountsRefferedBack->toArray();
        $this->assertModelData($chartOfAccountsRefferedBack->toArray(), $dbChartOfAccountsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateChartOfAccountsRefferedBack()
    {
        $chartOfAccountsRefferedBack = $this->makeChartOfAccountsRefferedBack();
        $fakeChartOfAccountsRefferedBack = $this->fakeChartOfAccountsRefferedBackData();
        $updatedChartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepo->update($fakeChartOfAccountsRefferedBack, $chartOfAccountsRefferedBack->id);
        $this->assertModelData($fakeChartOfAccountsRefferedBack, $updatedChartOfAccountsRefferedBack->toArray());
        $dbChartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepo->find($chartOfAccountsRefferedBack->id);
        $this->assertModelData($fakeChartOfAccountsRefferedBack, $dbChartOfAccountsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteChartOfAccountsRefferedBack()
    {
        $chartOfAccountsRefferedBack = $this->makeChartOfAccountsRefferedBack();
        $resp = $this->chartOfAccountsRefferedBackRepo->delete($chartOfAccountsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(ChartOfAccountsRefferedBack::find($chartOfAccountsRefferedBack->id), 'ChartOfAccountsRefferedBack should not exist in DB');
    }
}
