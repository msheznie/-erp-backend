<?php

use App\Models\DirectInvoiceDetailsRefferedBack;
use App\Repositories\DirectInvoiceDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectInvoiceDetailsRefferedBackRepositoryTest extends TestCase
{
    use MakeDirectInvoiceDetailsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DirectInvoiceDetailsRefferedBackRepository
     */
    protected $directInvoiceDetailsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->directInvoiceDetailsRefferedBackRepo = App::make(DirectInvoiceDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDirectInvoiceDetailsRefferedBack()
    {
        $directInvoiceDetailsRefferedBack = $this->fakeDirectInvoiceDetailsRefferedBackData();
        $createdDirectInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepo->create($directInvoiceDetailsRefferedBack);
        $createdDirectInvoiceDetailsRefferedBack = $createdDirectInvoiceDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdDirectInvoiceDetailsRefferedBack);
        $this->assertNotNull($createdDirectInvoiceDetailsRefferedBack['id'], 'Created DirectInvoiceDetailsRefferedBack must have id specified');
        $this->assertNotNull(DirectInvoiceDetailsRefferedBack::find($createdDirectInvoiceDetailsRefferedBack['id']), 'DirectInvoiceDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($directInvoiceDetailsRefferedBack, $createdDirectInvoiceDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadDirectInvoiceDetailsRefferedBack()
    {
        $directInvoiceDetailsRefferedBack = $this->makeDirectInvoiceDetailsRefferedBack();
        $dbDirectInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepo->find($directInvoiceDetailsRefferedBack->id);
        $dbDirectInvoiceDetailsRefferedBack = $dbDirectInvoiceDetailsRefferedBack->toArray();
        $this->assertModelData($directInvoiceDetailsRefferedBack->toArray(), $dbDirectInvoiceDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateDirectInvoiceDetailsRefferedBack()
    {
        $directInvoiceDetailsRefferedBack = $this->makeDirectInvoiceDetailsRefferedBack();
        $fakeDirectInvoiceDetailsRefferedBack = $this->fakeDirectInvoiceDetailsRefferedBackData();
        $updatedDirectInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepo->update($fakeDirectInvoiceDetailsRefferedBack, $directInvoiceDetailsRefferedBack->id);
        $this->assertModelData($fakeDirectInvoiceDetailsRefferedBack, $updatedDirectInvoiceDetailsRefferedBack->toArray());
        $dbDirectInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepo->find($directInvoiceDetailsRefferedBack->id);
        $this->assertModelData($fakeDirectInvoiceDetailsRefferedBack, $dbDirectInvoiceDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDirectInvoiceDetailsRefferedBack()
    {
        $directInvoiceDetailsRefferedBack = $this->makeDirectInvoiceDetailsRefferedBack();
        $resp = $this->directInvoiceDetailsRefferedBackRepo->delete($directInvoiceDetailsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(DirectInvoiceDetailsRefferedBack::find($directInvoiceDetailsRefferedBack->id), 'DirectInvoiceDetailsRefferedBack should not exist in DB');
    }
}
