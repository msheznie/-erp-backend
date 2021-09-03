<?php namespace Tests\Repositories;

use App\Models\ERPAssetVerificationDetailReferredback;
use App\Repositories\ERPAssetVerificationDetailReferredbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ERPAssetVerificationDetailReferredbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ERPAssetVerificationDetailReferredbackRepository
     */
    protected $eRPAssetVerificationDetailReferredbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->eRPAssetVerificationDetailReferredbackRepo = \App::make(ERPAssetVerificationDetailReferredbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_e_r_p_asset_verification_detail_referredback()
    {
        $eRPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->make()->toArray();

        $createdERPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepo->create($eRPAssetVerificationDetailReferredback);

        $createdERPAssetVerificationDetailReferredback = $createdERPAssetVerificationDetailReferredback->toArray();
        $this->assertArrayHasKey('id', $createdERPAssetVerificationDetailReferredback);
        $this->assertNotNull($createdERPAssetVerificationDetailReferredback['id'], 'Created ERPAssetVerificationDetailReferredback must have id specified');
        $this->assertNotNull(ERPAssetVerificationDetailReferredback::find($createdERPAssetVerificationDetailReferredback['id']), 'ERPAssetVerificationDetailReferredback with given id must be in DB');
        $this->assertModelData($eRPAssetVerificationDetailReferredback, $createdERPAssetVerificationDetailReferredback);
    }

    /**
     * @test read
     */
    public function test_read_e_r_p_asset_verification_detail_referredback()
    {
        $eRPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->create();

        $dbERPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepo->find($eRPAssetVerificationDetailReferredback->id);

        $dbERPAssetVerificationDetailReferredback = $dbERPAssetVerificationDetailReferredback->toArray();
        $this->assertModelData($eRPAssetVerificationDetailReferredback->toArray(), $dbERPAssetVerificationDetailReferredback);
    }

    /**
     * @test update
     */
    public function test_update_e_r_p_asset_verification_detail_referredback()
    {
        $eRPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->create();
        $fakeERPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->make()->toArray();

        $updatedERPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepo->update($fakeERPAssetVerificationDetailReferredback, $eRPAssetVerificationDetailReferredback->id);

        $this->assertModelData($fakeERPAssetVerificationDetailReferredback, $updatedERPAssetVerificationDetailReferredback->toArray());
        $dbERPAssetVerificationDetailReferredback = $this->eRPAssetVerificationDetailReferredbackRepo->find($eRPAssetVerificationDetailReferredback->id);
        $this->assertModelData($fakeERPAssetVerificationDetailReferredback, $dbERPAssetVerificationDetailReferredback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_e_r_p_asset_verification_detail_referredback()
    {
        $eRPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->create();

        $resp = $this->eRPAssetVerificationDetailReferredbackRepo->delete($eRPAssetVerificationDetailReferredback->id);

        $this->assertTrue($resp);
        $this->assertNull(ERPAssetVerificationDetailReferredback::find($eRPAssetVerificationDetailReferredback->id), 'ERPAssetVerificationDetailReferredback should not exist in DB');
    }
}
