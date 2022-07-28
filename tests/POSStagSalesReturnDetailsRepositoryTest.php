<?php namespace Tests\Repositories;

use App\Models\POSStagSalesReturnDetails;
use App\Repositories\POSStagSalesReturnDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSStagSalesReturnDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSStagSalesReturnDetailsRepository
     */
    protected $pOSStagSalesReturnDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSStagSalesReturnDetailsRepo = \App::make(POSStagSalesReturnDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_stag_sales_return_details()
    {
        $pOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->make()->toArray();

        $createdPOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepo->create($pOSStagSalesReturnDetails);

        $createdPOSStagSalesReturnDetails = $createdPOSStagSalesReturnDetails->toArray();
        $this->assertArrayHasKey('id', $createdPOSStagSalesReturnDetails);
        $this->assertNotNull($createdPOSStagSalesReturnDetails['id'], 'Created POSStagSalesReturnDetails must have id specified');
        $this->assertNotNull(POSStagSalesReturnDetails::find($createdPOSStagSalesReturnDetails['id']), 'POSStagSalesReturnDetails with given id must be in DB');
        $this->assertModelData($pOSStagSalesReturnDetails, $createdPOSStagSalesReturnDetails);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_stag_sales_return_details()
    {
        $pOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->create();

        $dbPOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepo->find($pOSStagSalesReturnDetails->id);

        $dbPOSStagSalesReturnDetails = $dbPOSStagSalesReturnDetails->toArray();
        $this->assertModelData($pOSStagSalesReturnDetails->toArray(), $dbPOSStagSalesReturnDetails);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_stag_sales_return_details()
    {
        $pOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->create();
        $fakePOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->make()->toArray();

        $updatedPOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepo->update($fakePOSStagSalesReturnDetails, $pOSStagSalesReturnDetails->id);

        $this->assertModelData($fakePOSStagSalesReturnDetails, $updatedPOSStagSalesReturnDetails->toArray());
        $dbPOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepo->find($pOSStagSalesReturnDetails->id);
        $this->assertModelData($fakePOSStagSalesReturnDetails, $dbPOSStagSalesReturnDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_stag_sales_return_details()
    {
        $pOSStagSalesReturnDetails = factory(POSStagSalesReturnDetails::class)->create();

        $resp = $this->pOSStagSalesReturnDetailsRepo->delete($pOSStagSalesReturnDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(POSStagSalesReturnDetails::find($pOSStagSalesReturnDetails->id), 'POSStagSalesReturnDetails should not exist in DB');
    }
}
