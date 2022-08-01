<?php namespace Tests\Repositories;

use App\Models\POSStagMenueSalesItemDetail;
use App\Repositories\POSStagMenueSalesItemDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSStagMenueSalesItemDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSStagMenueSalesItemDetailRepository
     */
    protected $pOSStagMenueSalesItemDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSStagMenueSalesItemDetailRepo = \App::make(POSStagMenueSalesItemDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_stag_menue_sales_item_detail()
    {
        $pOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->make()->toArray();

        $createdPOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepo->create($pOSStagMenueSalesItemDetail);

        $createdPOSStagMenueSalesItemDetail = $createdPOSStagMenueSalesItemDetail->toArray();
        $this->assertArrayHasKey('id', $createdPOSStagMenueSalesItemDetail);
        $this->assertNotNull($createdPOSStagMenueSalesItemDetail['id'], 'Created POSStagMenueSalesItemDetail must have id specified');
        $this->assertNotNull(POSStagMenueSalesItemDetail::find($createdPOSStagMenueSalesItemDetail['id']), 'POSStagMenueSalesItemDetail with given id must be in DB');
        $this->assertModelData($pOSStagMenueSalesItemDetail, $createdPOSStagMenueSalesItemDetail);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_stag_menue_sales_item_detail()
    {
        $pOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->create();

        $dbPOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepo->find($pOSStagMenueSalesItemDetail->id);

        $dbPOSStagMenueSalesItemDetail = $dbPOSStagMenueSalesItemDetail->toArray();
        $this->assertModelData($pOSStagMenueSalesItemDetail->toArray(), $dbPOSStagMenueSalesItemDetail);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_stag_menue_sales_item_detail()
    {
        $pOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->create();
        $fakePOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->make()->toArray();

        $updatedPOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepo->update($fakePOSStagMenueSalesItemDetail, $pOSStagMenueSalesItemDetail->id);

        $this->assertModelData($fakePOSStagMenueSalesItemDetail, $updatedPOSStagMenueSalesItemDetail->toArray());
        $dbPOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepo->find($pOSStagMenueSalesItemDetail->id);
        $this->assertModelData($fakePOSStagMenueSalesItemDetail, $dbPOSStagMenueSalesItemDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_stag_menue_sales_item_detail()
    {
        $pOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->create();

        $resp = $this->pOSStagMenueSalesItemDetailRepo->delete($pOSStagMenueSalesItemDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(POSStagMenueSalesItemDetail::find($pOSStagMenueSalesItemDetail->id), 'POSStagMenueSalesItemDetail should not exist in DB');
    }
}
