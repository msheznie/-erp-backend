<?php namespace Tests\Repositories;

use App\Models\LogUploadCustomerInvoice;
use App\Repositories\LogUploadCustomerInvoiceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LogUploadCustomerInvoiceRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogUploadCustomerInvoiceRepository
     */
    protected $logUploadCustomerInvoiceRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->logUploadCustomerInvoiceRepo = \App::make(LogUploadCustomerInvoiceRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_log_upload_customer_invoice()
    {
        $logUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->make()->toArray();

        $createdLogUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepo->create($logUploadCustomerInvoice);

        $createdLogUploadCustomerInvoice = $createdLogUploadCustomerInvoice->toArray();
        $this->assertArrayHasKey('id', $createdLogUploadCustomerInvoice);
        $this->assertNotNull($createdLogUploadCustomerInvoice['id'], 'Created LogUploadCustomerInvoice must have id specified');
        $this->assertNotNull(LogUploadCustomerInvoice::find($createdLogUploadCustomerInvoice['id']), 'LogUploadCustomerInvoice with given id must be in DB');
        $this->assertModelData($logUploadCustomerInvoice, $createdLogUploadCustomerInvoice);
    }

    /**
     * @test read
     */
    public function test_read_log_upload_customer_invoice()
    {
        $logUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->create();

        $dbLogUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepo->find($logUploadCustomerInvoice->id);

        $dbLogUploadCustomerInvoice = $dbLogUploadCustomerInvoice->toArray();
        $this->assertModelData($logUploadCustomerInvoice->toArray(), $dbLogUploadCustomerInvoice);
    }

    /**
     * @test update
     */
    public function test_update_log_upload_customer_invoice()
    {
        $logUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->create();
        $fakeLogUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->make()->toArray();

        $updatedLogUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepo->update($fakeLogUploadCustomerInvoice, $logUploadCustomerInvoice->id);

        $this->assertModelData($fakeLogUploadCustomerInvoice, $updatedLogUploadCustomerInvoice->toArray());
        $dbLogUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepo->find($logUploadCustomerInvoice->id);
        $this->assertModelData($fakeLogUploadCustomerInvoice, $dbLogUploadCustomerInvoice->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_log_upload_customer_invoice()
    {
        $logUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->create();

        $resp = $this->logUploadCustomerInvoiceRepo->delete($logUploadCustomerInvoice->id);

        $this->assertTrue($resp);
        $this->assertNull(LogUploadCustomerInvoice::find($logUploadCustomerInvoice->id), 'LogUploadCustomerInvoice should not exist in DB');
    }
}
