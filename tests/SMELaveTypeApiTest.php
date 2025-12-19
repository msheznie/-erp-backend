<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMELaveType;

class SMELaveTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_lave_type()
    {
        $sMELaveType = factory(SMELaveType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_lave_types', $sMELaveType
        );

        $this->assertApiResponse($sMELaveType);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_lave_type()
    {
        $sMELaveType = factory(SMELaveType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_lave_types/'.$sMELaveType->id
        );

        $this->assertApiResponse($sMELaveType->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_lave_type()
    {
        $sMELaveType = factory(SMELaveType::class)->create();
        $editedSMELaveType = factory(SMELaveType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_lave_types/'.$sMELaveType->id,
            $editedSMELaveType
        );

        $this->assertApiResponse($editedSMELaveType);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_lave_type()
    {
        $sMELaveType = factory(SMELaveType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_lave_types/'.$sMELaveType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_lave_types/'.$sMELaveType->id
        );

        $this->response->assertStatus(404);
    }
}
