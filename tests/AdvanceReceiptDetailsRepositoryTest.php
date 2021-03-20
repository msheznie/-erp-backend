<?php namespace Tests\Repositories;

use App\Models\AdvanceReceiptDetails;
use App\Repositories\AdvanceReceiptDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AdvanceReceiptDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AdvanceReceiptDetailsRepository
     */
    protected $advanceReceiptDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->advanceReceiptDetailsRepo = \App::make(AdvanceReceiptDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_advance_receipt_details()
    {
        $advanceReceiptDetails = factory(AdvanceReceiptDetails::class)->make()->toArray();

        $createdAdvanceReceiptDetails = $this->advanceReceiptDetailsRepo->create($advanceReceiptDetails);

        $createdAdvanceReceiptDetails = $createdAdvanceReceiptDetails->toArray();
        $this->assertArrayHasKey('id', $createdAdvanceReceiptDetails);
        $this->assertNotNull($createdAdvanceReceiptDetails['id'], 'Created AdvanceReceiptDetails must have id specified');
        $this->assertNotNull(AdvanceReceiptDetails::find($createdAdvanceReceiptDetails['id']), 'AdvanceReceiptDetails with given id must be in DB');
        $this->assertModelData($advanceReceiptDetails, $createdAdvanceReceiptDetails);
    }

    /**
     * @test read
     */
    public function test_read_advance_receipt_details()
    {
        $advanceReceiptDetails = factory(AdvanceReceiptDetails::class)->create();

        $dbAdvanceReceiptDetails = $this->advanceReceiptDetailsRepo->find($advanceReceiptDetails->id);

        $dbAdvanceReceiptDetails = $dbAdvanceReceiptDetails->toArray();
        $this->assertModelData($advanceReceiptDetails->toArray(), $dbAdvanceReceiptDetails);
    }

    /**
     * @test update
     */
    public function test_update_advance_receipt_details()
    {
        $advanceReceiptDetails = factory(AdvanceReceiptDetails::class)->create();
        $fakeAdvanceReceiptDetails = factory(AdvanceReceiptDetails::class)->make()->toArray();

        $updatedAdvanceReceiptDetails = $this->advanceReceiptDetailsRepo->update($fakeAdvanceReceiptDetails, $advanceReceiptDetails->id);

        $this->assertModelData($fakeAdvanceReceiptDetails, $updatedAdvanceReceiptDetails->toArray());
        $dbAdvanceReceiptDetails = $this->advanceReceiptDetailsRepo->find($advanceReceiptDetails->id);
        $this->assertModelData($fakeAdvanceReceiptDetails, $dbAdvanceReceiptDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_advance_receipt_details()
    {
        $advanceReceiptDetails = factory(AdvanceReceiptDetails::class)->create();

        $resp = $this->advanceReceiptDetailsRepo->delete($advanceReceiptDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(AdvanceReceiptDetails::find($advanceReceiptDetails->id), 'AdvanceReceiptDetails should not exist in DB');
    }
}
