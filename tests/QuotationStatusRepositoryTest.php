<?php namespace Tests\Repositories;

use App\Models\QuotationStatus;
use App\Repositories\QuotationStatusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class QuotationStatusRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuotationStatusRepository
     */
    protected $quotationStatusRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->quotationStatusRepo = \App::make(QuotationStatusRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_quotation_status()
    {
        $quotationStatus = factory(QuotationStatus::class)->make()->toArray();

        $createdQuotationStatus = $this->quotationStatusRepo->create($quotationStatus);

        $createdQuotationStatus = $createdQuotationStatus->toArray();
        $this->assertArrayHasKey('id', $createdQuotationStatus);
        $this->assertNotNull($createdQuotationStatus['id'], 'Created QuotationStatus must have id specified');
        $this->assertNotNull(QuotationStatus::find($createdQuotationStatus['id']), 'QuotationStatus with given id must be in DB');
        $this->assertModelData($quotationStatus, $createdQuotationStatus);
    }

    /**
     * @test read
     */
    public function test_read_quotation_status()
    {
        $quotationStatus = factory(QuotationStatus::class)->create();

        $dbQuotationStatus = $this->quotationStatusRepo->find($quotationStatus->id);

        $dbQuotationStatus = $dbQuotationStatus->toArray();
        $this->assertModelData($quotationStatus->toArray(), $dbQuotationStatus);
    }

    /**
     * @test update
     */
    public function test_update_quotation_status()
    {
        $quotationStatus = factory(QuotationStatus::class)->create();
        $fakeQuotationStatus = factory(QuotationStatus::class)->make()->toArray();

        $updatedQuotationStatus = $this->quotationStatusRepo->update($fakeQuotationStatus, $quotationStatus->id);

        $this->assertModelData($fakeQuotationStatus, $updatedQuotationStatus->toArray());
        $dbQuotationStatus = $this->quotationStatusRepo->find($quotationStatus->id);
        $this->assertModelData($fakeQuotationStatus, $dbQuotationStatus->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_quotation_status()
    {
        $quotationStatus = factory(QuotationStatus::class)->create();

        $resp = $this->quotationStatusRepo->delete($quotationStatus->id);

        $this->assertTrue($resp);
        $this->assertNull(QuotationStatus::find($quotationStatus->id), 'QuotationStatus should not exist in DB');
    }
}
