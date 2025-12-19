<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\GrvDetailsPrn;

class GrvDetailsPrnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_grv_details_prn()
    {
        $grvDetailsPrn = factory(GrvDetailsPrn::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/grv_details_prns', $grvDetailsPrn
        );

        $this->assertApiResponse($grvDetailsPrn);
    }

    /**
     * @test
     */
    public function test_read_grv_details_prn()
    {
        $grvDetailsPrn = factory(GrvDetailsPrn::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/grv_details_prns/'.$grvDetailsPrn->id
        );

        $this->assertApiResponse($grvDetailsPrn->toArray());
    }

    /**
     * @test
     */
    public function test_update_grv_details_prn()
    {
        $grvDetailsPrn = factory(GrvDetailsPrn::class)->create();
        $editedGrvDetailsPrn = factory(GrvDetailsPrn::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/grv_details_prns/'.$grvDetailsPrn->id,
            $editedGrvDetailsPrn
        );

        $this->assertApiResponse($editedGrvDetailsPrn);
    }

    /**
     * @test
     */
    public function test_delete_grv_details_prn()
    {
        $grvDetailsPrn = factory(GrvDetailsPrn::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/grv_details_prns/'.$grvDetailsPrn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/grv_details_prns/'.$grvDetailsPrn->id
        );

        $this->response->assertStatus(404);
    }
}
