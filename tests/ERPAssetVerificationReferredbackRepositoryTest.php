<?php namespace Tests\Repositories;

use App\Models\ERPAssetVerificationReferredback;
use App\Repositories\ERPAssetVerificationReferredbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ERPAssetVerificationReferredbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ERPAssetVerificationReferredbackRepository
     */
    protected $eRPAssetVerificationReferredbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->eRPAssetVerificationReferredbackRepo = \App::make(ERPAssetVerificationReferredbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_e_r_p_asset_verification_referredback()
    {
        $eRPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->make()->toArray();

        $createdERPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepo->create($eRPAssetVerificationReferredback);

        $createdERPAssetVerificationReferredback = $createdERPAssetVerificationReferredback->toArray();
        $this->assertArrayHasKey('id', $createdERPAssetVerificationReferredback);
        $this->assertNotNull($createdERPAssetVerificationReferredback['id'], 'Created ERPAssetVerificationReferredback must have id specified');
        $this->assertNotNull(ERPAssetVerificationReferredback::find($createdERPAssetVerificationReferredback['id']), 'ERPAssetVerificationReferredback with given id must be in DB');
        $this->assertModelData($eRPAssetVerificationReferredback, $createdERPAssetVerificationReferredback);
    }

    /**
     * @test read
     */
    public function test_read_e_r_p_asset_verification_referredback()
    {
        $eRPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->create();

        $dbERPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepo->find($eRPAssetVerificationReferredback->id);

        $dbERPAssetVerificationReferredback = $dbERPAssetVerificationReferredback->toArray();
        $this->assertModelData($eRPAssetVerificationReferredback->toArray(), $dbERPAssetVerificationReferredback);
    }

    /**
     * @test update
     */
    public function test_update_e_r_p_asset_verification_referredback()
    {
        $eRPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->create();
        $fakeERPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->make()->toArray();

        $updatedERPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepo->update($fakeERPAssetVerificationReferredback, $eRPAssetVerificationReferredback->id);

        $this->assertModelData($fakeERPAssetVerificationReferredback, $updatedERPAssetVerificationReferredback->toArray());
        $dbERPAssetVerificationReferredback = $this->eRPAssetVerificationReferredbackRepo->find($eRPAssetVerificationReferredback->id);
        $this->assertModelData($fakeERPAssetVerificationReferredback, $dbERPAssetVerificationReferredback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_e_r_p_asset_verification_referredback()
    {
        $eRPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->create();

        $resp = $this->eRPAssetVerificationReferredbackRepo->delete($eRPAssetVerificationReferredback->id);

        $this->assertTrue($resp);
        $this->assertNull(ERPAssetVerificationReferredback::find($eRPAssetVerificationReferredback->id), 'ERPAssetVerificationReferredback should not exist in DB');
    }
}
