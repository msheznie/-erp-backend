<?php namespace Tests\Repositories;

use App\Models\POSStagMenuSalesItem;
use App\Repositories\POSStagMenuSalesItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSStagMenuSalesItemRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSStagMenuSalesItemRepository
     */
    protected $pOSStagMenuSalesItemRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSStagMenuSalesItemRepo = \App::make(POSStagMenuSalesItemRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_stag_menu_sales_item()
    {
        $pOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->make()->toArray();

        $createdPOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepo->create($pOSStagMenuSalesItem);

        $createdPOSStagMenuSalesItem = $createdPOSStagMenuSalesItem->toArray();
        $this->assertArrayHasKey('id', $createdPOSStagMenuSalesItem);
        $this->assertNotNull($createdPOSStagMenuSalesItem['id'], 'Created POSStagMenuSalesItem must have id specified');
        $this->assertNotNull(POSStagMenuSalesItem::find($createdPOSStagMenuSalesItem['id']), 'POSStagMenuSalesItem with given id must be in DB');
        $this->assertModelData($pOSStagMenuSalesItem, $createdPOSStagMenuSalesItem);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_stag_menu_sales_item()
    {
        $pOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->create();

        $dbPOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepo->find($pOSStagMenuSalesItem->id);

        $dbPOSStagMenuSalesItem = $dbPOSStagMenuSalesItem->toArray();
        $this->assertModelData($pOSStagMenuSalesItem->toArray(), $dbPOSStagMenuSalesItem);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_stag_menu_sales_item()
    {
        $pOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->create();
        $fakePOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->make()->toArray();

        $updatedPOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepo->update($fakePOSStagMenuSalesItem, $pOSStagMenuSalesItem->id);

        $this->assertModelData($fakePOSStagMenuSalesItem, $updatedPOSStagMenuSalesItem->toArray());
        $dbPOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepo->find($pOSStagMenuSalesItem->id);
        $this->assertModelData($fakePOSStagMenuSalesItem, $dbPOSStagMenuSalesItem->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_stag_menu_sales_item()
    {
        $pOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->create();

        $resp = $this->pOSStagMenuSalesItemRepo->delete($pOSStagMenuSalesItem->id);

        $this->assertTrue($resp);
        $this->assertNull(POSStagMenuSalesItem::find($pOSStagMenuSalesItem->id), 'POSStagMenuSalesItem should not exist in DB');
    }
}
