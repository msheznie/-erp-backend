<?php namespace Tests\Repositories;

use App\Models\TenderBoqItems;
use App\Repositories\TenderBoqItemsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderBoqItemsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderBoqItemsRepository
     */
    protected $tenderBoqItemsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderBoqItemsRepo = \App::make(TenderBoqItemsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_boq_items()
    {
        $tenderBoqItems = factory(TenderBoqItems::class)->make()->toArray();

        $createdTenderBoqItems = $this->tenderBoqItemsRepo->create($tenderBoqItems);

        $createdTenderBoqItems = $createdTenderBoqItems->toArray();
        $this->assertArrayHasKey('id', $createdTenderBoqItems);
        $this->assertNotNull($createdTenderBoqItems['id'], 'Created TenderBoqItems must have id specified');
        $this->assertNotNull(TenderBoqItems::find($createdTenderBoqItems['id']), 'TenderBoqItems with given id must be in DB');
        $this->assertModelData($tenderBoqItems, $createdTenderBoqItems);
    }

    /**
     * @test read
     */
    public function test_read_tender_boq_items()
    {
        $tenderBoqItems = factory(TenderBoqItems::class)->create();

        $dbTenderBoqItems = $this->tenderBoqItemsRepo->find($tenderBoqItems->id);

        $dbTenderBoqItems = $dbTenderBoqItems->toArray();
        $this->assertModelData($tenderBoqItems->toArray(), $dbTenderBoqItems);
    }

    /**
     * @test update
     */
    public function test_update_tender_boq_items()
    {
        $tenderBoqItems = factory(TenderBoqItems::class)->create();
        $fakeTenderBoqItems = factory(TenderBoqItems::class)->make()->toArray();

        $updatedTenderBoqItems = $this->tenderBoqItemsRepo->update($fakeTenderBoqItems, $tenderBoqItems->id);

        $this->assertModelData($fakeTenderBoqItems, $updatedTenderBoqItems->toArray());
        $dbTenderBoqItems = $this->tenderBoqItemsRepo->find($tenderBoqItems->id);
        $this->assertModelData($fakeTenderBoqItems, $dbTenderBoqItems->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_boq_items()
    {
        $tenderBoqItems = factory(TenderBoqItems::class)->create();

        $resp = $this->tenderBoqItemsRepo->delete($tenderBoqItems->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderBoqItems::find($tenderBoqItems->id), 'TenderBoqItems should not exist in DB');
    }
}
