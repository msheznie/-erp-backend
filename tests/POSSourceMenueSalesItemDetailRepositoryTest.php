<?php namespace Tests\Repositories;

use App\Models\POSSourceMenueSalesItemDetail;
use App\Repositories\POSSourceMenueSalesItemDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourceMenueSalesItemDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourceMenueSalesItemDetailRepository
     */
    protected $pOSSourceMenueSalesItemDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourceMenueSalesItemDetailRepo = \App::make(POSSourceMenueSalesItemDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_menue_sales_item_detail()
    {
        $pOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->make()->toArray();

        $createdPOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepo->create($pOSSourceMenueSalesItemDetail);

        $createdPOSSourceMenueSalesItemDetail = $createdPOSSourceMenueSalesItemDetail->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourceMenueSalesItemDetail);
        $this->assertNotNull($createdPOSSourceMenueSalesItemDetail['id'], 'Created POSSourceMenueSalesItemDetail must have id specified');
        $this->assertNotNull(POSSourceMenueSalesItemDetail::find($createdPOSSourceMenueSalesItemDetail['id']), 'POSSourceMenueSalesItemDetail with given id must be in DB');
        $this->assertModelData($pOSSourceMenueSalesItemDetail, $createdPOSSourceMenueSalesItemDetail);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_menue_sales_item_detail()
    {
        $pOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->create();

        $dbPOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepo->find($pOSSourceMenueSalesItemDetail->id);

        $dbPOSSourceMenueSalesItemDetail = $dbPOSSourceMenueSalesItemDetail->toArray();
        $this->assertModelData($pOSSourceMenueSalesItemDetail->toArray(), $dbPOSSourceMenueSalesItemDetail);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_menue_sales_item_detail()
    {
        $pOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->create();
        $fakePOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->make()->toArray();

        $updatedPOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepo->update($fakePOSSourceMenueSalesItemDetail, $pOSSourceMenueSalesItemDetail->id);

        $this->assertModelData($fakePOSSourceMenueSalesItemDetail, $updatedPOSSourceMenueSalesItemDetail->toArray());
        $dbPOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepo->find($pOSSourceMenueSalesItemDetail->id);
        $this->assertModelData($fakePOSSourceMenueSalesItemDetail, $dbPOSSourceMenueSalesItemDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_menue_sales_item_detail()
    {
        $pOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->create();

        $resp = $this->pOSSourceMenueSalesItemDetailRepo->delete($pOSSourceMenueSalesItemDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourceMenueSalesItemDetail::find($pOSSourceMenueSalesItemDetail->id), 'POSSourceMenueSalesItemDetail should not exist in DB');
    }
}
