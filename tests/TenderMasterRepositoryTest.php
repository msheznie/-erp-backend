<?php namespace Tests\Repositories;

use App\Models\TenderMaster;
use App\Repositories\TenderMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderMasterRepository
     */
    protected $tenderMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderMasterRepo = \App::make(TenderMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_master()
    {
        $tenderMaster = factory(TenderMaster::class)->make()->toArray();

        $createdTenderMaster = $this->tenderMasterRepo->create($tenderMaster);

        $createdTenderMaster = $createdTenderMaster->toArray();
        $this->assertArrayHasKey('id', $createdTenderMaster);
        $this->assertNotNull($createdTenderMaster['id'], 'Created TenderMaster must have id specified');
        $this->assertNotNull(TenderMaster::find($createdTenderMaster['id']), 'TenderMaster with given id must be in DB');
        $this->assertModelData($tenderMaster, $createdTenderMaster);
    }

    /**
     * @test read
     */
    public function test_read_tender_master()
    {
        $tenderMaster = factory(TenderMaster::class)->create();

        $dbTenderMaster = $this->tenderMasterRepo->find($tenderMaster->id);

        $dbTenderMaster = $dbTenderMaster->toArray();
        $this->assertModelData($tenderMaster->toArray(), $dbTenderMaster);
    }

    /**
     * @test update
     */
    public function test_update_tender_master()
    {
        $tenderMaster = factory(TenderMaster::class)->create();
        $fakeTenderMaster = factory(TenderMaster::class)->make()->toArray();

        $updatedTenderMaster = $this->tenderMasterRepo->update($fakeTenderMaster, $tenderMaster->id);

        $this->assertModelData($fakeTenderMaster, $updatedTenderMaster->toArray());
        $dbTenderMaster = $this->tenderMasterRepo->find($tenderMaster->id);
        $this->assertModelData($fakeTenderMaster, $dbTenderMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_master()
    {
        $tenderMaster = factory(TenderMaster::class)->create();

        $resp = $this->tenderMasterRepo->delete($tenderMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderMaster::find($tenderMaster->id), 'TenderMaster should not exist in DB');
    }
}
