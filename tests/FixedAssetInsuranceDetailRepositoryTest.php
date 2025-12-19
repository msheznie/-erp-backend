<?php

use App\Models\FixedAssetInsuranceDetail;
use App\Repositories\FixedAssetInsuranceDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetInsuranceDetailRepositoryTest extends TestCase
{
    use MakeFixedAssetInsuranceDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FixedAssetInsuranceDetailRepository
     */
    protected $fixedAssetInsuranceDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fixedAssetInsuranceDetailRepo = App::make(FixedAssetInsuranceDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFixedAssetInsuranceDetail()
    {
        $fixedAssetInsuranceDetail = $this->fakeFixedAssetInsuranceDetailData();
        $createdFixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepo->create($fixedAssetInsuranceDetail);
        $createdFixedAssetInsuranceDetail = $createdFixedAssetInsuranceDetail->toArray();
        $this->assertArrayHasKey('id', $createdFixedAssetInsuranceDetail);
        $this->assertNotNull($createdFixedAssetInsuranceDetail['id'], 'Created FixedAssetInsuranceDetail must have id specified');
        $this->assertNotNull(FixedAssetInsuranceDetail::find($createdFixedAssetInsuranceDetail['id']), 'FixedAssetInsuranceDetail with given id must be in DB');
        $this->assertModelData($fixedAssetInsuranceDetail, $createdFixedAssetInsuranceDetail);
    }

    /**
     * @test read
     */
    public function testReadFixedAssetInsuranceDetail()
    {
        $fixedAssetInsuranceDetail = $this->makeFixedAssetInsuranceDetail();
        $dbFixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepo->find($fixedAssetInsuranceDetail->id);
        $dbFixedAssetInsuranceDetail = $dbFixedAssetInsuranceDetail->toArray();
        $this->assertModelData($fixedAssetInsuranceDetail->toArray(), $dbFixedAssetInsuranceDetail);
    }

    /**
     * @test update
     */
    public function testUpdateFixedAssetInsuranceDetail()
    {
        $fixedAssetInsuranceDetail = $this->makeFixedAssetInsuranceDetail();
        $fakeFixedAssetInsuranceDetail = $this->fakeFixedAssetInsuranceDetailData();
        $updatedFixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepo->update($fakeFixedAssetInsuranceDetail, $fixedAssetInsuranceDetail->id);
        $this->assertModelData($fakeFixedAssetInsuranceDetail, $updatedFixedAssetInsuranceDetail->toArray());
        $dbFixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepo->find($fixedAssetInsuranceDetail->id);
        $this->assertModelData($fakeFixedAssetInsuranceDetail, $dbFixedAssetInsuranceDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFixedAssetInsuranceDetail()
    {
        $fixedAssetInsuranceDetail = $this->makeFixedAssetInsuranceDetail();
        $resp = $this->fixedAssetInsuranceDetailRepo->delete($fixedAssetInsuranceDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(FixedAssetInsuranceDetail::find($fixedAssetInsuranceDetail->id), 'FixedAssetInsuranceDetail should not exist in DB');
    }
}
