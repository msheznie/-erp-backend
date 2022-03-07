<?php namespace Tests\Repositories;

use App\Models\InterCompanyStockTransfer;
use App\Repositories\InterCompanyStockTransferRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class InterCompanyStockTransferRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var InterCompanyStockTransferRepository
     */
    protected $interCompanyStockTransferRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->interCompanyStockTransferRepo = \App::make(InterCompanyStockTransferRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_inter_company_stock_transfer()
    {
        $interCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->make()->toArray();

        $createdInterCompanyStockTransfer = $this->interCompanyStockTransferRepo->create($interCompanyStockTransfer);

        $createdInterCompanyStockTransfer = $createdInterCompanyStockTransfer->toArray();
        $this->assertArrayHasKey('id', $createdInterCompanyStockTransfer);
        $this->assertNotNull($createdInterCompanyStockTransfer['id'], 'Created InterCompanyStockTransfer must have id specified');
        $this->assertNotNull(InterCompanyStockTransfer::find($createdInterCompanyStockTransfer['id']), 'InterCompanyStockTransfer with given id must be in DB');
        $this->assertModelData($interCompanyStockTransfer, $createdInterCompanyStockTransfer);
    }

    /**
     * @test read
     */
    public function test_read_inter_company_stock_transfer()
    {
        $interCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->create();

        $dbInterCompanyStockTransfer = $this->interCompanyStockTransferRepo->find($interCompanyStockTransfer->id);

        $dbInterCompanyStockTransfer = $dbInterCompanyStockTransfer->toArray();
        $this->assertModelData($interCompanyStockTransfer->toArray(), $dbInterCompanyStockTransfer);
    }

    /**
     * @test update
     */
    public function test_update_inter_company_stock_transfer()
    {
        $interCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->create();
        $fakeInterCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->make()->toArray();

        $updatedInterCompanyStockTransfer = $this->interCompanyStockTransferRepo->update($fakeInterCompanyStockTransfer, $interCompanyStockTransfer->id);

        $this->assertModelData($fakeInterCompanyStockTransfer, $updatedInterCompanyStockTransfer->toArray());
        $dbInterCompanyStockTransfer = $this->interCompanyStockTransferRepo->find($interCompanyStockTransfer->id);
        $this->assertModelData($fakeInterCompanyStockTransfer, $dbInterCompanyStockTransfer->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_inter_company_stock_transfer()
    {
        $interCompanyStockTransfer = factory(InterCompanyStockTransfer::class)->create();

        $resp = $this->interCompanyStockTransferRepo->delete($interCompanyStockTransfer->id);

        $this->assertTrue($resp);
        $this->assertNull(InterCompanyStockTransfer::find($interCompanyStockTransfer->id), 'InterCompanyStockTransfer should not exist in DB');
    }
}
