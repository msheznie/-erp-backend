<?php namespace Tests\Repositories;

use App\Models\SalesReturnRefferedBack;
use App\Repositories\SalesReturnRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SalesReturnRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesReturnRefferedBackRepository
     */
    protected $salesReturnRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->salesReturnRefferedBackRepo = \App::make(SalesReturnRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sales_return_reffered_back()
    {
        $salesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->make()->toArray();

        $createdSalesReturnRefferedBack = $this->salesReturnRefferedBackRepo->create($salesReturnRefferedBack);

        $createdSalesReturnRefferedBack = $createdSalesReturnRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdSalesReturnRefferedBack);
        $this->assertNotNull($createdSalesReturnRefferedBack['id'], 'Created SalesReturnRefferedBack must have id specified');
        $this->assertNotNull(SalesReturnRefferedBack::find($createdSalesReturnRefferedBack['id']), 'SalesReturnRefferedBack with given id must be in DB');
        $this->assertModelData($salesReturnRefferedBack, $createdSalesReturnRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_sales_return_reffered_back()
    {
        $salesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->create();

        $dbSalesReturnRefferedBack = $this->salesReturnRefferedBackRepo->find($salesReturnRefferedBack->id);

        $dbSalesReturnRefferedBack = $dbSalesReturnRefferedBack->toArray();
        $this->assertModelData($salesReturnRefferedBack->toArray(), $dbSalesReturnRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_sales_return_reffered_back()
    {
        $salesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->create();
        $fakeSalesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->make()->toArray();

        $updatedSalesReturnRefferedBack = $this->salesReturnRefferedBackRepo->update($fakeSalesReturnRefferedBack, $salesReturnRefferedBack->id);

        $this->assertModelData($fakeSalesReturnRefferedBack, $updatedSalesReturnRefferedBack->toArray());
        $dbSalesReturnRefferedBack = $this->salesReturnRefferedBackRepo->find($salesReturnRefferedBack->id);
        $this->assertModelData($fakeSalesReturnRefferedBack, $dbSalesReturnRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sales_return_reffered_back()
    {
        $salesReturnRefferedBack = factory(SalesReturnRefferedBack::class)->create();

        $resp = $this->salesReturnRefferedBackRepo->delete($salesReturnRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(SalesReturnRefferedBack::find($salesReturnRefferedBack->id), 'SalesReturnRefferedBack should not exist in DB');
    }
}
