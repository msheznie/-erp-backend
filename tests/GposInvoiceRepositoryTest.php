<?php

use App\Models\GposInvoice;
use App\Repositories\GposInvoiceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposInvoiceRepositoryTest extends TestCase
{
    use MakeGposInvoiceTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GposInvoiceRepository
     */
    protected $gposInvoiceRepo;

    public function setUp()
    {
        parent::setUp();
        $this->gposInvoiceRepo = App::make(GposInvoiceRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGposInvoice()
    {
        $gposInvoice = $this->fakeGposInvoiceData();
        $createdGposInvoice = $this->gposInvoiceRepo->create($gposInvoice);
        $createdGposInvoice = $createdGposInvoice->toArray();
        $this->assertArrayHasKey('id', $createdGposInvoice);
        $this->assertNotNull($createdGposInvoice['id'], 'Created GposInvoice must have id specified');
        $this->assertNotNull(GposInvoice::find($createdGposInvoice['id']), 'GposInvoice with given id must be in DB');
        $this->assertModelData($gposInvoice, $createdGposInvoice);
    }

    /**
     * @test read
     */
    public function testReadGposInvoice()
    {
        $gposInvoice = $this->makeGposInvoice();
        $dbGposInvoice = $this->gposInvoiceRepo->find($gposInvoice->id);
        $dbGposInvoice = $dbGposInvoice->toArray();
        $this->assertModelData($gposInvoice->toArray(), $dbGposInvoice);
    }

    /**
     * @test update
     */
    public function testUpdateGposInvoice()
    {
        $gposInvoice = $this->makeGposInvoice();
        $fakeGposInvoice = $this->fakeGposInvoiceData();
        $updatedGposInvoice = $this->gposInvoiceRepo->update($fakeGposInvoice, $gposInvoice->id);
        $this->assertModelData($fakeGposInvoice, $updatedGposInvoice->toArray());
        $dbGposInvoice = $this->gposInvoiceRepo->find($gposInvoice->id);
        $this->assertModelData($fakeGposInvoice, $dbGposInvoice->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGposInvoice()
    {
        $gposInvoice = $this->makeGposInvoice();
        $resp = $this->gposInvoiceRepo->delete($gposInvoice->id);
        $this->assertTrue($resp);
        $this->assertNull(GposInvoice::find($gposInvoice->id), 'GposInvoice should not exist in DB');
    }
}
