<?php namespace Tests\Repositories;

use App\Models\TenderSiteVisitDates;
use App\Repositories\TenderSiteVisitDatesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderSiteVisitDatesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderSiteVisitDatesRepository
     */
    protected $tenderSiteVisitDatesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderSiteVisitDatesRepo = \App::make(TenderSiteVisitDatesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_site_visit_dates()
    {
        $tenderSiteVisitDates = factory(TenderSiteVisitDates::class)->make()->toArray();

        $createdTenderSiteVisitDates = $this->tenderSiteVisitDatesRepo->create($tenderSiteVisitDates);

        $createdTenderSiteVisitDates = $createdTenderSiteVisitDates->toArray();
        $this->assertArrayHasKey('id', $createdTenderSiteVisitDates);
        $this->assertNotNull($createdTenderSiteVisitDates['id'], 'Created TenderSiteVisitDates must have id specified');
        $this->assertNotNull(TenderSiteVisitDates::find($createdTenderSiteVisitDates['id']), 'TenderSiteVisitDates with given id must be in DB');
        $this->assertModelData($tenderSiteVisitDates, $createdTenderSiteVisitDates);
    }

    /**
     * @test read
     */
    public function test_read_tender_site_visit_dates()
    {
        $tenderSiteVisitDates = factory(TenderSiteVisitDates::class)->create();

        $dbTenderSiteVisitDates = $this->tenderSiteVisitDatesRepo->find($tenderSiteVisitDates->id);

        $dbTenderSiteVisitDates = $dbTenderSiteVisitDates->toArray();
        $this->assertModelData($tenderSiteVisitDates->toArray(), $dbTenderSiteVisitDates);
    }

    /**
     * @test update
     */
    public function test_update_tender_site_visit_dates()
    {
        $tenderSiteVisitDates = factory(TenderSiteVisitDates::class)->create();
        $fakeTenderSiteVisitDates = factory(TenderSiteVisitDates::class)->make()->toArray();

        $updatedTenderSiteVisitDates = $this->tenderSiteVisitDatesRepo->update($fakeTenderSiteVisitDates, $tenderSiteVisitDates->id);

        $this->assertModelData($fakeTenderSiteVisitDates, $updatedTenderSiteVisitDates->toArray());
        $dbTenderSiteVisitDates = $this->tenderSiteVisitDatesRepo->find($tenderSiteVisitDates->id);
        $this->assertModelData($fakeTenderSiteVisitDates, $dbTenderSiteVisitDates->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_site_visit_dates()
    {
        $tenderSiteVisitDates = factory(TenderSiteVisitDates::class)->create();

        $resp = $this->tenderSiteVisitDatesRepo->delete($tenderSiteVisitDates->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderSiteVisitDates::find($tenderSiteVisitDates->id), 'TenderSiteVisitDates should not exist in DB');
    }
}
