<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderMasterSupplier;

class TenderMasterSupplierApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_master_supplier()
    {
        $tenderMasterSupplier = factory(TenderMasterSupplier::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_master_suppliers', $tenderMasterSupplier
        );

        $this->assertApiResponse($tenderMasterSupplier);
    }

    /**
     * @test
     */
    public function test_read_tender_master_supplier()
    {
        $tenderMasterSupplier = factory(TenderMasterSupplier::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_master_suppliers/'.$tenderMasterSupplier->id
        );

        $this->assertApiResponse($tenderMasterSupplier->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_master_supplier()
    {
        $tenderMasterSupplier = factory(TenderMasterSupplier::class)->create();
        $editedTenderMasterSupplier = factory(TenderMasterSupplier::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_master_suppliers/'.$tenderMasterSupplier->id,
            $editedTenderMasterSupplier
        );

        $this->assertApiResponse($editedTenderMasterSupplier);
    }

    /**
     * @test
     */
    public function test_delete_tender_master_supplier()
    {
        $tenderMasterSupplier = factory(TenderMasterSupplier::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_master_suppliers/'.$tenderMasterSupplier->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_master_suppliers/'.$tenderMasterSupplier->id
        );

        $this->response->assertStatus(404);
    }
}
