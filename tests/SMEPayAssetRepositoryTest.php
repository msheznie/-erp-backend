<?php namespace Tests\Repositories;

use App\Models\SMEPayAsset;
use App\Repositories\SMEPayAssetRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMEPayAssetRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMEPayAssetRepository
     */
    protected $sMEPayAssetRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMEPayAssetRepo = \App::make(SMEPayAssetRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_pay_asset()
    {
        $sMEPayAsset = factory(SMEPayAsset::class)->make()->toArray();

        $createdSMEPayAsset = $this->sMEPayAssetRepo->create($sMEPayAsset);

        $createdSMEPayAsset = $createdSMEPayAsset->toArray();
        $this->assertArrayHasKey('id', $createdSMEPayAsset);
        $this->assertNotNull($createdSMEPayAsset['id'], 'Created SMEPayAsset must have id specified');
        $this->assertNotNull(SMEPayAsset::find($createdSMEPayAsset['id']), 'SMEPayAsset with given id must be in DB');
        $this->assertModelData($sMEPayAsset, $createdSMEPayAsset);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_pay_asset()
    {
        $sMEPayAsset = factory(SMEPayAsset::class)->create();

        $dbSMEPayAsset = $this->sMEPayAssetRepo->find($sMEPayAsset->id);

        $dbSMEPayAsset = $dbSMEPayAsset->toArray();
        $this->assertModelData($sMEPayAsset->toArray(), $dbSMEPayAsset);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_pay_asset()
    {
        $sMEPayAsset = factory(SMEPayAsset::class)->create();
        $fakeSMEPayAsset = factory(SMEPayAsset::class)->make()->toArray();

        $updatedSMEPayAsset = $this->sMEPayAssetRepo->update($fakeSMEPayAsset, $sMEPayAsset->id);

        $this->assertModelData($fakeSMEPayAsset, $updatedSMEPayAsset->toArray());
        $dbSMEPayAsset = $this->sMEPayAssetRepo->find($sMEPayAsset->id);
        $this->assertModelData($fakeSMEPayAsset, $dbSMEPayAsset->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_pay_asset()
    {
        $sMEPayAsset = factory(SMEPayAsset::class)->create();

        $resp = $this->sMEPayAssetRepo->delete($sMEPayAsset->id);

        $this->assertTrue($resp);
        $this->assertNull(SMEPayAsset::find($sMEPayAsset->id), 'SMEPayAsset should not exist in DB');
    }
}
