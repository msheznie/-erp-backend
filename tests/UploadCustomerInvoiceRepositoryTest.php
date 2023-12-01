<?php namespace Tests\Repositories;

use App\Models\UploadCustomerInvoice;
use App\Repositories\UploadCustomerInvoiceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class UploadCustomerInvoiceRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var UploadCustomerInvoiceRepository
     */
    protected $uploadCustomerInvoiceRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->uploadCustomerInvoiceRepo = \App::make(UploadCustomerInvoiceRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_upload_customer_invoice()
    {
        $uploadCustomerInvoice = factory(UploadCustomerInvoice::class)->make()->toArray();

        $createdUploadCustomerInvoice = $this->uploadCustomerInvoiceRepo->create($uploadCustomerInvoice);

        $createdUploadCustomerInvoice = $createdUploadCustomerInvoice->toArray();
        $this->assertArrayHasKey('id', $createdUploadCustomerInvoice);
        $this->assertNotNull($createdUploadCustomerInvoice['id'], 'Created UploadCustomerInvoice must have id specified');
        $this->assertNotNull(UploadCustomerInvoice::find($createdUploadCustomerInvoice['id']), 'UploadCustomerInvoice with given id must be in DB');
        $this->assertModelData($uploadCustomerInvoice, $createdUploadCustomerInvoice);
    }

    /**
     * @test read
     */
    public function test_read_upload_customer_invoice()
    {
        $uploadCustomerInvoice = factory(UploadCustomerInvoice::class)->create();

        $dbUploadCustomerInvoice = $this->uploadCustomerInvoiceRepo->find($uploadCustomerInvoice->id);

        $dbUploadCustomerInvoice = $dbUploadCustomerInvoice->toArray();
        $this->assertModelData($uploadCustomerInvoice->toArray(), $dbUploadCustomerInvoice);
    }

    /**
     * @test update
     */
    public function test_update_upload_customer_invoice()
    {
        $uploadCustomerInvoice = factory(UploadCustomerInvoice::class)->create();
        $fakeUploadCustomerInvoice = factory(UploadCustomerInvoice::class)->make()->toArray();

        $updatedUploadCustomerInvoice = $this->uploadCustomerInvoiceRepo->update($fakeUploadCustomerInvoice, $uploadCustomerInvoice->id);

        $this->assertModelData($fakeUploadCustomerInvoice, $updatedUploadCustomerInvoice->toArray());
        $dbUploadCustomerInvoice = $this->uploadCustomerInvoiceRepo->find($uploadCustomerInvoice->id);
        $this->assertModelData($fakeUploadCustomerInvoice, $dbUploadCustomerInvoice->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_upload_customer_invoice()
    {
        $uploadCustomerInvoice = factory(UploadCustomerInvoice::class)->create();

        $resp = $this->uploadCustomerInvoiceRepo->delete($uploadCustomerInvoice->id);

        $this->assertTrue($resp);
        $this->assertNull(UploadCustomerInvoice::find($uploadCustomerInvoice->id), 'UploadCustomerInvoice should not exist in DB');
    }
}
