<?php namespace Tests\Repositories;

use App\Models\TenderMasterReferred;
use App\Repositories\TenderMasterReferredRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderMasterReferredRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderMasterReferredRepository
     */
    protected $tenderMasterReferredRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderMasterReferredRepo = \App::make(TenderMasterReferredRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_master_referred()
    {
        $tenderMasterReferred = factory(TenderMasterReferred::class)->make()->toArray();

        $createdTenderMasterReferred = $this->tenderMasterReferredRepo->create($tenderMasterReferred);

        $createdTenderMasterReferred = $createdTenderMasterReferred->toArray();
        $this->assertArrayHasKey('id', $createdTenderMasterReferred);
        $this->assertNotNull($createdTenderMasterReferred['id'], 'Created TenderMasterReferred must have id specified');
        $this->assertNotNull(TenderMasterReferred::find($createdTenderMasterReferred['id']), 'TenderMasterReferred with given id must be in DB');
        $this->assertModelData($tenderMasterReferred, $createdTenderMasterReferred);
    }

    /**
     * @test read
     */
    public function test_read_tender_master_referred()
    {
        $tenderMasterReferred = factory(TenderMasterReferred::class)->create();

        $dbTenderMasterReferred = $this->tenderMasterReferredRepo->find($tenderMasterReferred->id);

        $dbTenderMasterReferred = $dbTenderMasterReferred->toArray();
        $this->assertModelData($tenderMasterReferred->toArray(), $dbTenderMasterReferred);
    }

    /**
     * @test update
     */
    public function test_update_tender_master_referred()
    {
        $tenderMasterReferred = factory(TenderMasterReferred::class)->create();
        $fakeTenderMasterReferred = factory(TenderMasterReferred::class)->make()->toArray();

        $updatedTenderMasterReferred = $this->tenderMasterReferredRepo->update($fakeTenderMasterReferred, $tenderMasterReferred->id);

        $this->assertModelData($fakeTenderMasterReferred, $updatedTenderMasterReferred->toArray());
        $dbTenderMasterReferred = $this->tenderMasterReferredRepo->find($tenderMasterReferred->id);
        $this->assertModelData($fakeTenderMasterReferred, $dbTenderMasterReferred->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_master_referred()
    {
        $tenderMasterReferred = factory(TenderMasterReferred::class)->create();

        $resp = $this->tenderMasterReferredRepo->delete($tenderMasterReferred->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderMasterReferred::find($tenderMasterReferred->id), 'TenderMasterReferred should not exist in DB');
    }
}
