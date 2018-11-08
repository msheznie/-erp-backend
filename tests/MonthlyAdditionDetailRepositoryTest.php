<?php

use App\Models\MonthlyAdditionDetail;
use App\Repositories\MonthlyAdditionDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MonthlyAdditionDetailRepositoryTest extends TestCase
{
    use MakeMonthlyAdditionDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MonthlyAdditionDetailRepository
     */
    protected $monthlyAdditionDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->monthlyAdditionDetailRepo = App::make(MonthlyAdditionDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMonthlyAdditionDetail()
    {
        $monthlyAdditionDetail = $this->fakeMonthlyAdditionDetailData();
        $createdMonthlyAdditionDetail = $this->monthlyAdditionDetailRepo->create($monthlyAdditionDetail);
        $createdMonthlyAdditionDetail = $createdMonthlyAdditionDetail->toArray();
        $this->assertArrayHasKey('id', $createdMonthlyAdditionDetail);
        $this->assertNotNull($createdMonthlyAdditionDetail['id'], 'Created MonthlyAdditionDetail must have id specified');
        $this->assertNotNull(MonthlyAdditionDetail::find($createdMonthlyAdditionDetail['id']), 'MonthlyAdditionDetail with given id must be in DB');
        $this->assertModelData($monthlyAdditionDetail, $createdMonthlyAdditionDetail);
    }

    /**
     * @test read
     */
    public function testReadMonthlyAdditionDetail()
    {
        $monthlyAdditionDetail = $this->makeMonthlyAdditionDetail();
        $dbMonthlyAdditionDetail = $this->monthlyAdditionDetailRepo->find($monthlyAdditionDetail->id);
        $dbMonthlyAdditionDetail = $dbMonthlyAdditionDetail->toArray();
        $this->assertModelData($monthlyAdditionDetail->toArray(), $dbMonthlyAdditionDetail);
    }

    /**
     * @test update
     */
    public function testUpdateMonthlyAdditionDetail()
    {
        $monthlyAdditionDetail = $this->makeMonthlyAdditionDetail();
        $fakeMonthlyAdditionDetail = $this->fakeMonthlyAdditionDetailData();
        $updatedMonthlyAdditionDetail = $this->monthlyAdditionDetailRepo->update($fakeMonthlyAdditionDetail, $monthlyAdditionDetail->id);
        $this->assertModelData($fakeMonthlyAdditionDetail, $updatedMonthlyAdditionDetail->toArray());
        $dbMonthlyAdditionDetail = $this->monthlyAdditionDetailRepo->find($monthlyAdditionDetail->id);
        $this->assertModelData($fakeMonthlyAdditionDetail, $dbMonthlyAdditionDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMonthlyAdditionDetail()
    {
        $monthlyAdditionDetail = $this->makeMonthlyAdditionDetail();
        $resp = $this->monthlyAdditionDetailRepo->delete($monthlyAdditionDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(MonthlyAdditionDetail::find($monthlyAdditionDetail->id), 'MonthlyAdditionDetail should not exist in DB');
    }
}
