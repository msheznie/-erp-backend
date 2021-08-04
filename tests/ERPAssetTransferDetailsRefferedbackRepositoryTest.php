<?php namespace Tests\Repositories;

use App\Models\ERPAssetTransferDetailsRefferedback;
use App\Repositories\ERPAssetTransferDetailsRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ERPAssetTransferDetailsRefferedbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ERPAssetTransferDetailsRefferedbackRepository
     */
    protected $eRPAssetTransferDetailsRefferedbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->eRPAssetTransferDetailsRefferedbackRepo = \App::make(ERPAssetTransferDetailsRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_e_r_p_asset_transfer_details_refferedback()
    {
        $eRPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->make()->toArray();

        $createdERPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepo->create($eRPAssetTransferDetailsRefferedback);

        $createdERPAssetTransferDetailsRefferedback = $createdERPAssetTransferDetailsRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdERPAssetTransferDetailsRefferedback);
        $this->assertNotNull($createdERPAssetTransferDetailsRefferedback['id'], 'Created ERPAssetTransferDetailsRefferedback must have id specified');
        $this->assertNotNull(ERPAssetTransferDetailsRefferedback::find($createdERPAssetTransferDetailsRefferedback['id']), 'ERPAssetTransferDetailsRefferedback with given id must be in DB');
        $this->assertModelData($eRPAssetTransferDetailsRefferedback, $createdERPAssetTransferDetailsRefferedback);
    }

    /**
     * @test read
     */
    public function test_read_e_r_p_asset_transfer_details_refferedback()
    {
        $eRPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->create();

        $dbERPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepo->find($eRPAssetTransferDetailsRefferedback->id);

        $dbERPAssetTransferDetailsRefferedback = $dbERPAssetTransferDetailsRefferedback->toArray();
        $this->assertModelData($eRPAssetTransferDetailsRefferedback->toArray(), $dbERPAssetTransferDetailsRefferedback);
    }

    /**
     * @test update
     */
    public function test_update_e_r_p_asset_transfer_details_refferedback()
    {
        $eRPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->create();
        $fakeERPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->make()->toArray();

        $updatedERPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepo->update($fakeERPAssetTransferDetailsRefferedback, $eRPAssetTransferDetailsRefferedback->id);

        $this->assertModelData($fakeERPAssetTransferDetailsRefferedback, $updatedERPAssetTransferDetailsRefferedback->toArray());
        $dbERPAssetTransferDetailsRefferedback = $this->eRPAssetTransferDetailsRefferedbackRepo->find($eRPAssetTransferDetailsRefferedback->id);
        $this->assertModelData($fakeERPAssetTransferDetailsRefferedback, $dbERPAssetTransferDetailsRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_e_r_p_asset_transfer_details_refferedback()
    {
        $eRPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->create();

        $resp = $this->eRPAssetTransferDetailsRefferedbackRepo->delete($eRPAssetTransferDetailsRefferedback->id);

        $this->assertTrue($resp);
        $this->assertNull(ERPAssetTransferDetailsRefferedback::find($eRPAssetTransferDetailsRefferedback->id), 'ERPAssetTransferDetailsRefferedback should not exist in DB');
    }
}
