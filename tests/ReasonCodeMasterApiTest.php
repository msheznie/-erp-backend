<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ReasonCodeMaster;

class ReasonCodeMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_reason_code_master()
    {
        $reasonCodeMaster = factory(ReasonCodeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/reason_code_masters', $reasonCodeMaster
        );

        $this->assertApiResponse($reasonCodeMaster);
    }

    /**
     * @test
     */
    public function test_read_reason_code_master()
    {
        $reasonCodeMaster = factory(ReasonCodeMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/reason_code_masters/'.$reasonCodeMaster->id
        );

        $this->assertApiResponse($reasonCodeMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_reason_code_master()
    {
        $reasonCodeMaster = factory(ReasonCodeMaster::class)->create();
        $editedReasonCodeMaster = factory(ReasonCodeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/reason_code_masters/'.$reasonCodeMaster->id,
            $editedReasonCodeMaster
        );

        $this->assertApiResponse($editedReasonCodeMaster);
    }

    /**
     * @test
     */
    public function test_delete_reason_code_master()
    {
        $reasonCodeMaster = factory(ReasonCodeMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/reason_code_masters/'.$reasonCodeMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/reason_code_masters/'.$reasonCodeMaster->id
        );

        $this->response->assertStatus(404);
    }
}
