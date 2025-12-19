<?php namespace Tests\Repositories;

use App\Models\SalesReturnDetailRefferedBack;
use App\Repositories\SalesReturnDetailRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SalesReturnDetailRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesReturnDetailRefferedBackRepository
     */
    protected $salesReturnDetailRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->salesReturnDetailRefferedBackRepo = \App::make(SalesReturnDetailRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sales_return_detail_reffered_back()
    {
        $salesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->make()->toArray();

        $createdSalesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepo->create($salesReturnDetailRefferedBack);

        $createdSalesReturnDetailRefferedBack = $createdSalesReturnDetailRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdSalesReturnDetailRefferedBack);
        $this->assertNotNull($createdSalesReturnDetailRefferedBack['id'], 'Created SalesReturnDetailRefferedBack must have id specified');
        $this->assertNotNull(SalesReturnDetailRefferedBack::find($createdSalesReturnDetailRefferedBack['id']), 'SalesReturnDetailRefferedBack with given id must be in DB');
        $this->assertModelData($salesReturnDetailRefferedBack, $createdSalesReturnDetailRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_sales_return_detail_reffered_back()
    {
        $salesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->create();

        $dbSalesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepo->find($salesReturnDetailRefferedBack->id);

        $dbSalesReturnDetailRefferedBack = $dbSalesReturnDetailRefferedBack->toArray();
        $this->assertModelData($salesReturnDetailRefferedBack->toArray(), $dbSalesReturnDetailRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_sales_return_detail_reffered_back()
    {
        $salesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->create();
        $fakeSalesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->make()->toArray();

        $updatedSalesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepo->update($fakeSalesReturnDetailRefferedBack, $salesReturnDetailRefferedBack->id);

        $this->assertModelData($fakeSalesReturnDetailRefferedBack, $updatedSalesReturnDetailRefferedBack->toArray());
        $dbSalesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepo->find($salesReturnDetailRefferedBack->id);
        $this->assertModelData($fakeSalesReturnDetailRefferedBack, $dbSalesReturnDetailRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sales_return_detail_reffered_back()
    {
        $salesReturnDetailRefferedBack = factory(SalesReturnDetailRefferedBack::class)->create();

        $resp = $this->salesReturnDetailRefferedBackRepo->delete($salesReturnDetailRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(SalesReturnDetailRefferedBack::find($salesReturnDetailRefferedBack->id), 'SalesReturnDetailRefferedBack should not exist in DB');
    }
}
