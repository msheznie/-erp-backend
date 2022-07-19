<?php namespace Tests\Repositories;

use App\Models\TenderCirculars;
use App\Repositories\TenderCircularsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderCircularsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderCircularsRepository
     */
    protected $tenderCircularsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderCircularsRepo = \App::make(TenderCircularsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_circulars()
    {
        $tenderCirculars = factory(TenderCirculars::class)->make()->toArray();

        $createdTenderCirculars = $this->tenderCircularsRepo->create($tenderCirculars);

        $createdTenderCirculars = $createdTenderCirculars->toArray();
        $this->assertArrayHasKey('id', $createdTenderCirculars);
        $this->assertNotNull($createdTenderCirculars['id'], 'Created TenderCirculars must have id specified');
        $this->assertNotNull(TenderCirculars::find($createdTenderCirculars['id']), 'TenderCirculars with given id must be in DB');
        $this->assertModelData($tenderCirculars, $createdTenderCirculars);
    }

    /**
     * @test read
     */
    public function test_read_tender_circulars()
    {
        $tenderCirculars = factory(TenderCirculars::class)->create();

        $dbTenderCirculars = $this->tenderCircularsRepo->find($tenderCirculars->id);

        $dbTenderCirculars = $dbTenderCirculars->toArray();
        $this->assertModelData($tenderCirculars->toArray(), $dbTenderCirculars);
    }

    /**
     * @test update
     */
    public function test_update_tender_circulars()
    {
        $tenderCirculars = factory(TenderCirculars::class)->create();
        $fakeTenderCirculars = factory(TenderCirculars::class)->make()->toArray();

        $updatedTenderCirculars = $this->tenderCircularsRepo->update($fakeTenderCirculars, $tenderCirculars->id);

        $this->assertModelData($fakeTenderCirculars, $updatedTenderCirculars->toArray());
        $dbTenderCirculars = $this->tenderCircularsRepo->find($tenderCirculars->id);
        $this->assertModelData($fakeTenderCirculars, $dbTenderCirculars->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_circulars()
    {
        $tenderCirculars = factory(TenderCirculars::class)->create();

        $resp = $this->tenderCircularsRepo->delete($tenderCirculars->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderCirculars::find($tenderCirculars->id), 'TenderCirculars should not exist in DB');
    }
}
