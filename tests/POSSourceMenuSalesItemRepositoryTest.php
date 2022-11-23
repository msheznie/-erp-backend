<?php namespace Tests\Repositories;

use App\Models\POSSourceMenuSalesItem;
use App\Repositories\POSSourceMenuSalesItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourceMenuSalesItemRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourceMenuSalesItemRepository
     */
    protected $pOSSourceMenuSalesItemRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourceMenuSalesItemRepo = \App::make(POSSourceMenuSalesItemRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_menu_sales_item()
    {
        $pOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->make()->toArray();

        $createdPOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepo->create($pOSSourceMenuSalesItem);

        $createdPOSSourceMenuSalesItem = $createdPOSSourceMenuSalesItem->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourceMenuSalesItem);
        $this->assertNotNull($createdPOSSourceMenuSalesItem['id'], 'Created POSSourceMenuSalesItem must have id specified');
        $this->assertNotNull(POSSourceMenuSalesItem::find($createdPOSSourceMenuSalesItem['id']), 'POSSourceMenuSalesItem with given id must be in DB');
        $this->assertModelData($pOSSourceMenuSalesItem, $createdPOSSourceMenuSalesItem);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_menu_sales_item()
    {
        $pOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->create();

        $dbPOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepo->find($pOSSourceMenuSalesItem->id);

        $dbPOSSourceMenuSalesItem = $dbPOSSourceMenuSalesItem->toArray();
        $this->assertModelData($pOSSourceMenuSalesItem->toArray(), $dbPOSSourceMenuSalesItem);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_menu_sales_item()
    {
        $pOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->create();
        $fakePOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->make()->toArray();

        $updatedPOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepo->update($fakePOSSourceMenuSalesItem, $pOSSourceMenuSalesItem->id);

        $this->assertModelData($fakePOSSourceMenuSalesItem, $updatedPOSSourceMenuSalesItem->toArray());
        $dbPOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepo->find($pOSSourceMenuSalesItem->id);
        $this->assertModelData($fakePOSSourceMenuSalesItem, $dbPOSSourceMenuSalesItem->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_menu_sales_item()
    {
        $pOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->create();

        $resp = $this->pOSSourceMenuSalesItemRepo->delete($pOSSourceMenuSalesItem->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourceMenuSalesItem::find($pOSSourceMenuSalesItem->id), 'POSSourceMenuSalesItem should not exist in DB');
    }
}
