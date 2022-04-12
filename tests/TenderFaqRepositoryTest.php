<?php namespace Tests\Repositories;

use App\Models\TenderFaq;
use App\Repositories\TenderFaqRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderFaqRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderFaqRepository
     */
    protected $tenderFaqRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderFaqRepo = \App::make(TenderFaqRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_faq()
    {
        $tenderFaq = factory(TenderFaq::class)->make()->toArray();

        $createdTenderFaq = $this->tenderFaqRepo->create($tenderFaq);

        $createdTenderFaq = $createdTenderFaq->toArray();
        $this->assertArrayHasKey('id', $createdTenderFaq);
        $this->assertNotNull($createdTenderFaq['id'], 'Created TenderFaq must have id specified');
        $this->assertNotNull(TenderFaq::find($createdTenderFaq['id']), 'TenderFaq with given id must be in DB');
        $this->assertModelData($tenderFaq, $createdTenderFaq);
    }

    /**
     * @test read
     */
    public function test_read_tender_faq()
    {
        $tenderFaq = factory(TenderFaq::class)->create();

        $dbTenderFaq = $this->tenderFaqRepo->find($tenderFaq->id);

        $dbTenderFaq = $dbTenderFaq->toArray();
        $this->assertModelData($tenderFaq->toArray(), $dbTenderFaq);
    }

    /**
     * @test update
     */
    public function test_update_tender_faq()
    {
        $tenderFaq = factory(TenderFaq::class)->create();
        $fakeTenderFaq = factory(TenderFaq::class)->make()->toArray();

        $updatedTenderFaq = $this->tenderFaqRepo->update($fakeTenderFaq, $tenderFaq->id);

        $this->assertModelData($fakeTenderFaq, $updatedTenderFaq->toArray());
        $dbTenderFaq = $this->tenderFaqRepo->find($tenderFaq->id);
        $this->assertModelData($fakeTenderFaq, $dbTenderFaq->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_faq()
    {
        $tenderFaq = factory(TenderFaq::class)->create();

        $resp = $this->tenderFaqRepo->delete($tenderFaq->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderFaq::find($tenderFaq->id), 'TenderFaq should not exist in DB');
    }
}
