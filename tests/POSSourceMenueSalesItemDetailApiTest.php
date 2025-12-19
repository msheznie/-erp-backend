<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSourceMenueSalesItemDetail;

class POSSourceMenueSalesItemDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_source_menue_sales_item_detail()
    {
        $pOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_source_menue_sales_item_details', $pOSSourceMenueSalesItemDetail
        );

        $this->assertApiResponse($pOSSourceMenueSalesItemDetail);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_source_menue_sales_item_detail()
    {
        $pOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menue_sales_item_details/'.$pOSSourceMenueSalesItemDetail->id
        );

        $this->assertApiResponse($pOSSourceMenueSalesItemDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_source_menue_sales_item_detail()
    {
        $pOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->create();
        $editedPOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_source_menue_sales_item_details/'.$pOSSourceMenueSalesItemDetail->id,
            $editedPOSSourceMenueSalesItemDetail
        );

        $this->assertApiResponse($editedPOSSourceMenueSalesItemDetail);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_source_menue_sales_item_detail()
    {
        $pOSSourceMenueSalesItemDetail = factory(POSSourceMenueSalesItemDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_source_menue_sales_item_details/'.$pOSSourceMenueSalesItemDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menue_sales_item_details/'.$pOSSourceMenueSalesItemDetail->id
        );

        $this->response->assertStatus(404);
    }
}
