<?php namespace Tests\Repositories;

use App\Models\POSSourceSalesReturn;
use App\Repositories\POSSourceSalesReturnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourceSalesReturnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourceSalesReturnRepository
     */
    protected $pOSSourceSalesReturnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourceSalesReturnRepo = \App::make(POSSourceSalesReturnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_sales_return()
    {
        $pOSSourceSalesReturn = factory(POSSourceSalesReturn::class)->make()->toArray();

        $createdPOSSourceSalesReturn = $this->pOSSourceSalesReturnRepo->create($pOSSourceSalesReturn);

        $createdPOSSourceSalesReturn = $createdPOSSourceSalesReturn->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourceSalesReturn);
        $this->assertNotNull($createdPOSSourceSalesReturn['id'], 'Created POSSourceSalesReturn must have id specified');
        $this->assertNotNull(POSSourceSalesReturn::find($createdPOSSourceSalesReturn['id']), 'POSSourceSalesReturn with given id must be in DB');
        $this->assertModelData($pOSSourceSalesReturn, $createdPOSSourceSalesReturn);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_sales_return()
    {
        $pOSSourceSalesReturn = factory(POSSourceSalesReturn::class)->create();

        $dbPOSSourceSalesReturn = $this->pOSSourceSalesReturnRepo->find($pOSSourceSalesReturn->id);

        $dbPOSSourceSalesReturn = $dbPOSSourceSalesReturn->toArray();
        $this->assertModelData($pOSSourceSalesReturn->toArray(), $dbPOSSourceSalesReturn);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_sales_return()
    {
        $pOSSourceSalesReturn = factory(POSSourceSalesReturn::class)->create();
        $fakePOSSourceSalesReturn = factory(POSSourceSalesReturn::class)->make()->toArray();

        $updatedPOSSourceSalesReturn = $this->pOSSourceSalesReturnRepo->update($fakePOSSourceSalesReturn, $pOSSourceSalesReturn->id);

        $this->assertModelData($fakePOSSourceSalesReturn, $updatedPOSSourceSalesReturn->toArray());
        $dbPOSSourceSalesReturn = $this->pOSSourceSalesReturnRepo->find($pOSSourceSalesReturn->id);
        $this->assertModelData($fakePOSSourceSalesReturn, $dbPOSSourceSalesReturn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_sales_return()
    {
        $pOSSourceSalesReturn = factory(POSSourceSalesReturn::class)->create();

        $resp = $this->pOSSourceSalesReturnRepo->delete($pOSSourceSalesReturn->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourceSalesReturn::find($pOSSourceSalesReturn->id), 'POSSourceSalesReturn should not exist in DB');
    }
}
