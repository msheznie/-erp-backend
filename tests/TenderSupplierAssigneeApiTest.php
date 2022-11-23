<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderSupplierAssignee;

class TenderSupplierAssigneeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_supplier_assignee()
    {
        $tenderSupplierAssignee = factory(TenderSupplierAssignee::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_supplier_assignees', $tenderSupplierAssignee
        );

        $this->assertApiResponse($tenderSupplierAssignee);
    }

    /**
     * @test
     */
    public function test_read_tender_supplier_assignee()
    {
        $tenderSupplierAssignee = factory(TenderSupplierAssignee::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_supplier_assignees/'.$tenderSupplierAssignee->id
        );

        $this->assertApiResponse($tenderSupplierAssignee->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_supplier_assignee()
    {
        $tenderSupplierAssignee = factory(TenderSupplierAssignee::class)->create();
        $editedTenderSupplierAssignee = factory(TenderSupplierAssignee::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_supplier_assignees/'.$tenderSupplierAssignee->id,
            $editedTenderSupplierAssignee
        );

        $this->assertApiResponse($editedTenderSupplierAssignee);
    }

    /**
     * @test
     */
    public function test_delete_tender_supplier_assignee()
    {
        $tenderSupplierAssignee = factory(TenderSupplierAssignee::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_supplier_assignees/'.$tenderSupplierAssignee->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_supplier_assignees/'.$tenderSupplierAssignee->id
        );

        $this->response->assertStatus(404);
    }
}
