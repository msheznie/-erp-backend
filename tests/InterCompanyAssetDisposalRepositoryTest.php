<?php namespace Tests\Repositories;

use App\Models\InterCompanyAssetDisposal;
use App\Repositories\InterCompanyAssetDisposalRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class InterCompanyAssetDisposalRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var InterCompanyAssetDisposalRepository
     */
    protected $interCompanyAssetDisposalRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->interCompanyAssetDisposalRepo = \App::make(InterCompanyAssetDisposalRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_inter_company_asset_disposal()
    {
        $interCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->make()->toArray();

        $createdInterCompanyAssetDisposal = $this->interCompanyAssetDisposalRepo->create($interCompanyAssetDisposal);

        $createdInterCompanyAssetDisposal = $createdInterCompanyAssetDisposal->toArray();
        $this->assertArrayHasKey('id', $createdInterCompanyAssetDisposal);
        $this->assertNotNull($createdInterCompanyAssetDisposal['id'], 'Created InterCompanyAssetDisposal must have id specified');
        $this->assertNotNull(InterCompanyAssetDisposal::find($createdInterCompanyAssetDisposal['id']), 'InterCompanyAssetDisposal with given id must be in DB');
        $this->assertModelData($interCompanyAssetDisposal, $createdInterCompanyAssetDisposal);
    }

    /**
     * @test read
     */
    public function test_read_inter_company_asset_disposal()
    {
        $interCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->create();

        $dbInterCompanyAssetDisposal = $this->interCompanyAssetDisposalRepo->find($interCompanyAssetDisposal->id);

        $dbInterCompanyAssetDisposal = $dbInterCompanyAssetDisposal->toArray();
        $this->assertModelData($interCompanyAssetDisposal->toArray(), $dbInterCompanyAssetDisposal);
    }

    /**
     * @test update
     */
    public function test_update_inter_company_asset_disposal()
    {
        $interCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->create();
        $fakeInterCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->make()->toArray();

        $updatedInterCompanyAssetDisposal = $this->interCompanyAssetDisposalRepo->update($fakeInterCompanyAssetDisposal, $interCompanyAssetDisposal->id);

        $this->assertModelData($fakeInterCompanyAssetDisposal, $updatedInterCompanyAssetDisposal->toArray());
        $dbInterCompanyAssetDisposal = $this->interCompanyAssetDisposalRepo->find($interCompanyAssetDisposal->id);
        $this->assertModelData($fakeInterCompanyAssetDisposal, $dbInterCompanyAssetDisposal->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_inter_company_asset_disposal()
    {
        $interCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->create();

        $resp = $this->interCompanyAssetDisposalRepo->delete($interCompanyAssetDisposal->id);

        $this->assertTrue($resp);
        $this->assertNull(InterCompanyAssetDisposal::find($interCompanyAssetDisposal->id), 'InterCompanyAssetDisposal should not exist in DB');
    }
}
