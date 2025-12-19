<?php namespace Tests\Repositories;

use App\Models\POSSourceSalesReturnDetails;
use App\Repositories\POSSourceSalesReturnDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourceSalesReturnDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourceSalesReturnDetailsRepository
     */
    protected $pOSSourceSalesReturnDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourceSalesReturnDetailsRepo = \App::make(POSSourceSalesReturnDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_sales_return_details()
    {
        $pOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->make()->toArray();

        $createdPOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepo->create($pOSSourceSalesReturnDetails);

        $createdPOSSourceSalesReturnDetails = $createdPOSSourceSalesReturnDetails->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourceSalesReturnDetails);
        $this->assertNotNull($createdPOSSourceSalesReturnDetails['id'], 'Created POSSourceSalesReturnDetails must have id specified');
        $this->assertNotNull(POSSourceSalesReturnDetails::find($createdPOSSourceSalesReturnDetails['id']), 'POSSourceSalesReturnDetails with given id must be in DB');
        $this->assertModelData($pOSSourceSalesReturnDetails, $createdPOSSourceSalesReturnDetails);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_sales_return_details()
    {
        $pOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->create();

        $dbPOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepo->find($pOSSourceSalesReturnDetails->id);

        $dbPOSSourceSalesReturnDetails = $dbPOSSourceSalesReturnDetails->toArray();
        $this->assertModelData($pOSSourceSalesReturnDetails->toArray(), $dbPOSSourceSalesReturnDetails);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_sales_return_details()
    {
        $pOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->create();
        $fakePOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->make()->toArray();

        $updatedPOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepo->update($fakePOSSourceSalesReturnDetails, $pOSSourceSalesReturnDetails->id);

        $this->assertModelData($fakePOSSourceSalesReturnDetails, $updatedPOSSourceSalesReturnDetails->toArray());
        $dbPOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepo->find($pOSSourceSalesReturnDetails->id);
        $this->assertModelData($fakePOSSourceSalesReturnDetails, $dbPOSSourceSalesReturnDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_sales_return_details()
    {
        $pOSSourceSalesReturnDetails = factory(POSSourceSalesReturnDetails::class)->create();

        $resp = $this->pOSSourceSalesReturnDetailsRepo->delete($pOSSourceSalesReturnDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourceSalesReturnDetails::find($pOSSourceSalesReturnDetails->id), 'POSSourceSalesReturnDetails should not exist in DB');
    }
}
