<?php namespace Tests\Repositories;

use App\Models\ERPAssetTransferDetail;
use App\Repositories\ERPAssetTransferDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ERPAssetTransferDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ERPAssetTransferDetailRepository
     */
    protected $eRPAssetTransferDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->eRPAssetTransferDetailRepo = \App::make(ERPAssetTransferDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_e_r_p_asset_transfer_detail()
    {
        $eRPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->make()->toArray();

        $createdERPAssetTransferDetail = $this->eRPAssetTransferDetailRepo->create($eRPAssetTransferDetail);

        $createdERPAssetTransferDetail = $createdERPAssetTransferDetail->toArray();
        $this->assertArrayHasKey('id', $createdERPAssetTransferDetail);
        $this->assertNotNull($createdERPAssetTransferDetail['id'], 'Created ERPAssetTransferDetail must have id specified');
        $this->assertNotNull(ERPAssetTransferDetail::find($createdERPAssetTransferDetail['id']), 'ERPAssetTransferDetail with given id must be in DB');
        $this->assertModelData($eRPAssetTransferDetail, $createdERPAssetTransferDetail);
    }

    /**
     * @test read
     */
    public function test_read_e_r_p_asset_transfer_detail()
    {
        $eRPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->create();

        $dbERPAssetTransferDetail = $this->eRPAssetTransferDetailRepo->find($eRPAssetTransferDetail->id);

        $dbERPAssetTransferDetail = $dbERPAssetTransferDetail->toArray();
        $this->assertModelData($eRPAssetTransferDetail->toArray(), $dbERPAssetTransferDetail);
    }

    /**
     * @test update
     */
    public function test_update_e_r_p_asset_transfer_detail()
    {
        $eRPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->create();
        $fakeERPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->make()->toArray();

        $updatedERPAssetTransferDetail = $this->eRPAssetTransferDetailRepo->update($fakeERPAssetTransferDetail, $eRPAssetTransferDetail->id);

        $this->assertModelData($fakeERPAssetTransferDetail, $updatedERPAssetTransferDetail->toArray());
        $dbERPAssetTransferDetail = $this->eRPAssetTransferDetailRepo->find($eRPAssetTransferDetail->id);
        $this->assertModelData($fakeERPAssetTransferDetail, $dbERPAssetTransferDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_e_r_p_asset_transfer_detail()
    {
        $eRPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->create();

        $resp = $this->eRPAssetTransferDetailRepo->delete($eRPAssetTransferDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(ERPAssetTransferDetail::find($eRPAssetTransferDetail->id), 'ERPAssetTransferDetail should not exist in DB');
    }
}
