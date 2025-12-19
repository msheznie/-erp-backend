<?php namespace Tests\Repositories;

use App\Models\TenderMainWorks;
use App\Repositories\TenderMainWorksRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderMainWorksRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderMainWorksRepository
     */
    protected $tenderMainWorksRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderMainWorksRepo = \App::make(TenderMainWorksRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_main_works()
    {
        $tenderMainWorks = factory(TenderMainWorks::class)->make()->toArray();

        $createdTenderMainWorks = $this->tenderMainWorksRepo->create($tenderMainWorks);

        $createdTenderMainWorks = $createdTenderMainWorks->toArray();
        $this->assertArrayHasKey('id', $createdTenderMainWorks);
        $this->assertNotNull($createdTenderMainWorks['id'], 'Created TenderMainWorks must have id specified');
        $this->assertNotNull(TenderMainWorks::find($createdTenderMainWorks['id']), 'TenderMainWorks with given id must be in DB');
        $this->assertModelData($tenderMainWorks, $createdTenderMainWorks);
    }

    /**
     * @test read
     */
    public function test_read_tender_main_works()
    {
        $tenderMainWorks = factory(TenderMainWorks::class)->create();

        $dbTenderMainWorks = $this->tenderMainWorksRepo->find($tenderMainWorks->id);

        $dbTenderMainWorks = $dbTenderMainWorks->toArray();
        $this->assertModelData($tenderMainWorks->toArray(), $dbTenderMainWorks);
    }

    /**
     * @test update
     */
    public function test_update_tender_main_works()
    {
        $tenderMainWorks = factory(TenderMainWorks::class)->create();
        $fakeTenderMainWorks = factory(TenderMainWorks::class)->make()->toArray();

        $updatedTenderMainWorks = $this->tenderMainWorksRepo->update($fakeTenderMainWorks, $tenderMainWorks->id);

        $this->assertModelData($fakeTenderMainWorks, $updatedTenderMainWorks->toArray());
        $dbTenderMainWorks = $this->tenderMainWorksRepo->find($tenderMainWorks->id);
        $this->assertModelData($fakeTenderMainWorks, $dbTenderMainWorks->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_main_works()
    {
        $tenderMainWorks = factory(TenderMainWorks::class)->create();

        $resp = $this->tenderMainWorksRepo->delete($tenderMainWorks->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderMainWorks::find($tenderMainWorks->id), 'TenderMainWorks should not exist in DB');
    }
}
