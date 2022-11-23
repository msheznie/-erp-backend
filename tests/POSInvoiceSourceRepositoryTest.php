<?php namespace Tests\Repositories;

use App\Models\POSInvoiceSource;
use App\Repositories\POSInvoiceSourceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSInvoiceSourceRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSInvoiceSourceRepository
     */
    protected $pOSInvoiceSourceRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSInvoiceSourceRepo = \App::make(POSInvoiceSourceRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_invoice_source()
    {
        $pOSInvoiceSource = factory(POSInvoiceSource::class)->make()->toArray();

        $createdPOSInvoiceSource = $this->pOSInvoiceSourceRepo->create($pOSInvoiceSource);

        $createdPOSInvoiceSource = $createdPOSInvoiceSource->toArray();
        $this->assertArrayHasKey('id', $createdPOSInvoiceSource);
        $this->assertNotNull($createdPOSInvoiceSource['id'], 'Created POSInvoiceSource must have id specified');
        $this->assertNotNull(POSInvoiceSource::find($createdPOSInvoiceSource['id']), 'POSInvoiceSource with given id must be in DB');
        $this->assertModelData($pOSInvoiceSource, $createdPOSInvoiceSource);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_invoice_source()
    {
        $pOSInvoiceSource = factory(POSInvoiceSource::class)->create();

        $dbPOSInvoiceSource = $this->pOSInvoiceSourceRepo->find($pOSInvoiceSource->id);

        $dbPOSInvoiceSource = $dbPOSInvoiceSource->toArray();
        $this->assertModelData($pOSInvoiceSource->toArray(), $dbPOSInvoiceSource);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_invoice_source()
    {
        $pOSInvoiceSource = factory(POSInvoiceSource::class)->create();
        $fakePOSInvoiceSource = factory(POSInvoiceSource::class)->make()->toArray();

        $updatedPOSInvoiceSource = $this->pOSInvoiceSourceRepo->update($fakePOSInvoiceSource, $pOSInvoiceSource->id);

        $this->assertModelData($fakePOSInvoiceSource, $updatedPOSInvoiceSource->toArray());
        $dbPOSInvoiceSource = $this->pOSInvoiceSourceRepo->find($pOSInvoiceSource->id);
        $this->assertModelData($fakePOSInvoiceSource, $dbPOSInvoiceSource->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_invoice_source()
    {
        $pOSInvoiceSource = factory(POSInvoiceSource::class)->create();

        $resp = $this->pOSInvoiceSourceRepo->delete($pOSInvoiceSource->id);

        $this->assertTrue($resp);
        $this->assertNull(POSInvoiceSource::find($pOSInvoiceSource->id), 'POSInvoiceSource should not exist in DB');
    }
}
