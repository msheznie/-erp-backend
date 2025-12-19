<?php

use App\Models\GposInvoicePayments;
use App\Repositories\GposInvoicePaymentsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposInvoicePaymentsRepositoryTest extends TestCase
{
    use MakeGposInvoicePaymentsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GposInvoicePaymentsRepository
     */
    protected $gposInvoicePaymentsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->gposInvoicePaymentsRepo = App::make(GposInvoicePaymentsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGposInvoicePayments()
    {
        $gposInvoicePayments = $this->fakeGposInvoicePaymentsData();
        $createdGposInvoicePayments = $this->gposInvoicePaymentsRepo->create($gposInvoicePayments);
        $createdGposInvoicePayments = $createdGposInvoicePayments->toArray();
        $this->assertArrayHasKey('id', $createdGposInvoicePayments);
        $this->assertNotNull($createdGposInvoicePayments['id'], 'Created GposInvoicePayments must have id specified');
        $this->assertNotNull(GposInvoicePayments::find($createdGposInvoicePayments['id']), 'GposInvoicePayments with given id must be in DB');
        $this->assertModelData($gposInvoicePayments, $createdGposInvoicePayments);
    }

    /**
     * @test read
     */
    public function testReadGposInvoicePayments()
    {
        $gposInvoicePayments = $this->makeGposInvoicePayments();
        $dbGposInvoicePayments = $this->gposInvoicePaymentsRepo->find($gposInvoicePayments->id);
        $dbGposInvoicePayments = $dbGposInvoicePayments->toArray();
        $this->assertModelData($gposInvoicePayments->toArray(), $dbGposInvoicePayments);
    }

    /**
     * @test update
     */
    public function testUpdateGposInvoicePayments()
    {
        $gposInvoicePayments = $this->makeGposInvoicePayments();
        $fakeGposInvoicePayments = $this->fakeGposInvoicePaymentsData();
        $updatedGposInvoicePayments = $this->gposInvoicePaymentsRepo->update($fakeGposInvoicePayments, $gposInvoicePayments->id);
        $this->assertModelData($fakeGposInvoicePayments, $updatedGposInvoicePayments->toArray());
        $dbGposInvoicePayments = $this->gposInvoicePaymentsRepo->find($gposInvoicePayments->id);
        $this->assertModelData($fakeGposInvoicePayments, $dbGposInvoicePayments->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGposInvoicePayments()
    {
        $gposInvoicePayments = $this->makeGposInvoicePayments();
        $resp = $this->gposInvoicePaymentsRepo->delete($gposInvoicePayments->id);
        $this->assertTrue($resp);
        $this->assertNull(GposInvoicePayments::find($gposInvoicePayments->id), 'GposInvoicePayments should not exist in DB');
    }
}
