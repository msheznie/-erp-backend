<?php namespace Tests\Repositories;

use App\Models\SalesReturn;
use App\Repositories\SalesReturnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SalesReturnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesReturnRepository
     */
    protected $salesReturnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->salesReturnRepo = \App::make(SalesReturnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sales_return()
    {
        $salesReturn = factory(SalesReturn::class)->make()->toArray();

        $createdSalesReturn = $this->salesReturnRepo->create($salesReturn);

        $createdSalesReturn = $createdSalesReturn->toArray();
        $this->assertArrayHasKey('id', $createdSalesReturn);
        $this->assertNotNull($createdSalesReturn['id'], 'Created SalesReturn must have id specified');
        $this->assertNotNull(SalesReturn::find($createdSalesReturn['id']), 'SalesReturn with given id must be in DB');
        $this->assertModelData($salesReturn, $createdSalesReturn);
    }

    /**
     * @test read
     */
    public function test_read_sales_return()
    {
        $salesReturn = factory(SalesReturn::class)->create();

        $dbSalesReturn = $this->salesReturnRepo->find($salesReturn->id);

        $dbSalesReturn = $dbSalesReturn->toArray();
        $this->assertModelData($salesReturn->toArray(), $dbSalesReturn);
    }

    /**
     * @test update
     */
    public function test_update_sales_return()
    {
        $salesReturn = factory(SalesReturn::class)->create();
        $fakeSalesReturn = factory(SalesReturn::class)->make()->toArray();

        $updatedSalesReturn = $this->salesReturnRepo->update($fakeSalesReturn, $salesReturn->id);

        $this->assertModelData($fakeSalesReturn, $updatedSalesReturn->toArray());
        $dbSalesReturn = $this->salesReturnRepo->find($salesReturn->id);
        $this->assertModelData($fakeSalesReturn, $dbSalesReturn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sales_return()
    {
        $salesReturn = factory(SalesReturn::class)->create();

        $resp = $this->salesReturnRepo->delete($salesReturn->id);

        $this->assertTrue($resp);
        $this->assertNull(SalesReturn::find($salesReturn->id), 'SalesReturn should not exist in DB');
    }
}
