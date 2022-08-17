<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PoCutoffJobData;

class PoCutoffJobDataApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_po_cutoff_job_data()
    {
        $poCutoffJobData = factory(PoCutoffJobData::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/po_cutoff_job_datas', $poCutoffJobData
        );

        $this->assertApiResponse($poCutoffJobData);
    }

    /**
     * @test
     */
    public function test_read_po_cutoff_job_data()
    {
        $poCutoffJobData = factory(PoCutoffJobData::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/po_cutoff_job_datas/'.$poCutoffJobData->id
        );

        $this->assertApiResponse($poCutoffJobData->toArray());
    }

    /**
     * @test
     */
    public function test_update_po_cutoff_job_data()
    {
        $poCutoffJobData = factory(PoCutoffJobData::class)->create();
        $editedPoCutoffJobData = factory(PoCutoffJobData::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/po_cutoff_job_datas/'.$poCutoffJobData->id,
            $editedPoCutoffJobData
        );

        $this->assertApiResponse($editedPoCutoffJobData);
    }

    /**
     * @test
     */
    public function test_delete_po_cutoff_job_data()
    {
        $poCutoffJobData = factory(PoCutoffJobData::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/po_cutoff_job_datas/'.$poCutoffJobData->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/po_cutoff_job_datas/'.$poCutoffJobData->id
        );

        $this->response->assertStatus(404);
    }
}
