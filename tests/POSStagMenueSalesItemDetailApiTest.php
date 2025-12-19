<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSStagMenueSalesItemDetail;

class POSStagMenueSalesItemDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_stag_menue_sales_item_detail()
    {
        $pOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_stag_menue_sales_item_details', $pOSStagMenueSalesItemDetail
        );

        $this->assertApiResponse($pOSStagMenueSalesItemDetail);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_stag_menue_sales_item_detail()
    {
        $pOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menue_sales_item_details/'.$pOSStagMenueSalesItemDetail->id
        );

        $this->assertApiResponse($pOSStagMenueSalesItemDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_stag_menue_sales_item_detail()
    {
        $pOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->create();
        $editedPOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_stag_menue_sales_item_details/'.$pOSStagMenueSalesItemDetail->id,
            $editedPOSStagMenueSalesItemDetail
        );

        $this->assertApiResponse($editedPOSStagMenueSalesItemDetail);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_stag_menue_sales_item_detail()
    {
        $pOSStagMenueSalesItemDetail = factory(POSStagMenueSalesItemDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_stag_menue_sales_item_details/'.$pOSStagMenueSalesItemDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menue_sales_item_details/'.$pOSStagMenueSalesItemDetail->id
        );

        $this->response->assertStatus(404);
    }
}
