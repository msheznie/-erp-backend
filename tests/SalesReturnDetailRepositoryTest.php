<?php namespace Tests\Repositories;

use App\Models\SalesReturnDetail;
use App\Repositories\SalesReturnDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SalesReturnDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesReturnDetailRepository
     */
    protected $salesReturnDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->salesReturnDetailRepo = \App::make(SalesReturnDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sales_return_detail()
    {
        $salesReturnDetail = factory(SalesReturnDetail::class)->make()->toArray();

        $createdSalesReturnDetail = $this->salesReturnDetailRepo->create($salesReturnDetail);

        $createdSalesReturnDetail = $createdSalesReturnDetail->toArray();
        $this->assertArrayHasKey('id', $createdSalesReturnDetail);
        $this->assertNotNull($createdSalesReturnDetail['id'], 'Created SalesReturnDetail must have id specified');
        $this->assertNotNull(SalesReturnDetail::find($createdSalesReturnDetail['id']), 'SalesReturnDetail with given id must be in DB');
        $this->assertModelData($salesReturnDetail, $createdSalesReturnDetail);
    }

    /**
     * @test read
     */
    public function test_read_sales_return_detail()
    {
        $salesReturnDetail = factory(SalesReturnDetail::class)->create();

        $dbSalesReturnDetail = $this->salesReturnDetailRepo->find($salesReturnDetail->id);

        $dbSalesReturnDetail = $dbSalesReturnDetail->toArray();
        $this->assertModelData($salesReturnDetail->toArray(), $dbSalesReturnDetail);
    }

    /**
     * @test update
     */
    public function test_update_sales_return_detail()
    {
        $salesReturnDetail = factory(SalesReturnDetail::class)->create();
        $fakeSalesReturnDetail = factory(SalesReturnDetail::class)->make()->toArray();

        $updatedSalesReturnDetail = $this->salesReturnDetailRepo->update($fakeSalesReturnDetail, $salesReturnDetail->id);

        $this->assertModelData($fakeSalesReturnDetail, $updatedSalesReturnDetail->toArray());
        $dbSalesReturnDetail = $this->salesReturnDetailRepo->find($salesReturnDetail->id);
        $this->assertModelData($fakeSalesReturnDetail, $dbSalesReturnDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sales_return_detail()
    {
        $salesReturnDetail = factory(SalesReturnDetail::class)->create();

        $resp = $this->salesReturnDetailRepo->delete($salesReturnDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(SalesReturnDetail::find($salesReturnDetail->id), 'SalesReturnDetail should not exist in DB');
    }
}
