<?php namespace Tests\Repositories;

use App\Models\SrmBidDocumentattachments;
use App\Repositories\SrmBidDocumentattachmentsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrmBidDocumentattachmentsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrmBidDocumentattachmentsRepository
     */
    protected $srmBidDocumentattachmentsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srmBidDocumentattachmentsRepo = \App::make(SrmBidDocumentattachmentsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srm_bid_documentattachments()
    {
        $srmBidDocumentattachments = factory(SrmBidDocumentattachments::class)->make()->toArray();

        $createdSrmBidDocumentattachments = $this->srmBidDocumentattachmentsRepo->create($srmBidDocumentattachments);

        $createdSrmBidDocumentattachments = $createdSrmBidDocumentattachments->toArray();
        $this->assertArrayHasKey('id', $createdSrmBidDocumentattachments);
        $this->assertNotNull($createdSrmBidDocumentattachments['id'], 'Created SrmBidDocumentattachments must have id specified');
        $this->assertNotNull(SrmBidDocumentattachments::find($createdSrmBidDocumentattachments['id']), 'SrmBidDocumentattachments with given id must be in DB');
        $this->assertModelData($srmBidDocumentattachments, $createdSrmBidDocumentattachments);
    }

    /**
     * @test read
     */
    public function test_read_srm_bid_documentattachments()
    {
        $srmBidDocumentattachments = factory(SrmBidDocumentattachments::class)->create();

        $dbSrmBidDocumentattachments = $this->srmBidDocumentattachmentsRepo->find($srmBidDocumentattachments->id);

        $dbSrmBidDocumentattachments = $dbSrmBidDocumentattachments->toArray();
        $this->assertModelData($srmBidDocumentattachments->toArray(), $dbSrmBidDocumentattachments);
    }

    /**
     * @test update
     */
    public function test_update_srm_bid_documentattachments()
    {
        $srmBidDocumentattachments = factory(SrmBidDocumentattachments::class)->create();
        $fakeSrmBidDocumentattachments = factory(SrmBidDocumentattachments::class)->make()->toArray();

        $updatedSrmBidDocumentattachments = $this->srmBidDocumentattachmentsRepo->update($fakeSrmBidDocumentattachments, $srmBidDocumentattachments->id);

        $this->assertModelData($fakeSrmBidDocumentattachments, $updatedSrmBidDocumentattachments->toArray());
        $dbSrmBidDocumentattachments = $this->srmBidDocumentattachmentsRepo->find($srmBidDocumentattachments->id);
        $this->assertModelData($fakeSrmBidDocumentattachments, $dbSrmBidDocumentattachments->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srm_bid_documentattachments()
    {
        $srmBidDocumentattachments = factory(SrmBidDocumentattachments::class)->create();

        $resp = $this->srmBidDocumentattachmentsRepo->delete($srmBidDocumentattachments->id);

        $this->assertTrue($resp);
        $this->assertNull(SrmBidDocumentattachments::find($srmBidDocumentattachments->id), 'SrmBidDocumentattachments should not exist in DB');
    }
}
