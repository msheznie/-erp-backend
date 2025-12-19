<?php namespace Tests\Repositories;

use App\Models\ERPAssetTransfer;
use App\Repositories\ERPAssetTransferRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ERPAssetTransferRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ERPAssetTransferRepository
     */
    protected $eRPAssetTransferRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->eRPAssetTransferRepo = \App::make(ERPAssetTransferRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_e_r_p_asset_transfer()
    {
        $eRPAssetTransfer = factory(ERPAssetTransfer::class)->make()->toArray();

        $createdERPAssetTransfer = $this->eRPAssetTransferRepo->create($eRPAssetTransfer);

        $createdERPAssetTransfer = $createdERPAssetTransfer->toArray();
        $this->assertArrayHasKey('id', $createdERPAssetTransfer);
        $this->assertNotNull($createdERPAssetTransfer['id'], 'Created ERPAssetTransfer must have id specified');
        $this->assertNotNull(ERPAssetTransfer::find($createdERPAssetTransfer['id']), 'ERPAssetTransfer with given id must be in DB');
        $this->assertModelData($eRPAssetTransfer, $createdERPAssetTransfer);
    }

    /**
     * @test read
     */
    public function test_read_e_r_p_asset_transfer()
    {
        $eRPAssetTransfer = factory(ERPAssetTransfer::class)->create();

        $dbERPAssetTransfer = $this->eRPAssetTransferRepo->find($eRPAssetTransfer->id);

        $dbERPAssetTransfer = $dbERPAssetTransfer->toArray();
        $this->assertModelData($eRPAssetTransfer->toArray(), $dbERPAssetTransfer);
    }

    /**
     * @test update
     */
    public function test_update_e_r_p_asset_transfer()
    {
        $eRPAssetTransfer = factory(ERPAssetTransfer::class)->create();
        $fakeERPAssetTransfer = factory(ERPAssetTransfer::class)->make()->toArray();

        $updatedERPAssetTransfer = $this->eRPAssetTransferRepo->update($fakeERPAssetTransfer, $eRPAssetTransfer->id);

        $this->assertModelData($fakeERPAssetTransfer, $updatedERPAssetTransfer->toArray());
        $dbERPAssetTransfer = $this->eRPAssetTransferRepo->find($eRPAssetTransfer->id);
        $this->assertModelData($fakeERPAssetTransfer, $dbERPAssetTransfer->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_e_r_p_asset_transfer()
    {
        $eRPAssetTransfer = factory(ERPAssetTransfer::class)->create();

        $resp = $this->eRPAssetTransferRepo->delete($eRPAssetTransfer->id);

        $this->assertTrue($resp);
        $this->assertNull(ERPAssetTransfer::find($eRPAssetTransfer->id), 'ERPAssetTransfer should not exist in DB');
    }
}
