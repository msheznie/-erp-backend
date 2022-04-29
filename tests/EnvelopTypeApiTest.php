<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EnvelopType;

class EnvelopTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_envelop_type()
    {
        $envelopType = factory(EnvelopType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/envelop_types', $envelopType
        );

        $this->assertApiResponse($envelopType);
    }

    /**
     * @test
     */
    public function test_read_envelop_type()
    {
        $envelopType = factory(EnvelopType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/envelop_types/'.$envelopType->id
        );

        $this->assertApiResponse($envelopType->toArray());
    }

    /**
     * @test
     */
    public function test_update_envelop_type()
    {
        $envelopType = factory(EnvelopType::class)->create();
        $editedEnvelopType = factory(EnvelopType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/envelop_types/'.$envelopType->id,
            $editedEnvelopType
        );

        $this->assertApiResponse($editedEnvelopType);
    }

    /**
     * @test
     */
    public function test_delete_envelop_type()
    {
        $envelopType = factory(EnvelopType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/envelop_types/'.$envelopType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/envelop_types/'.$envelopType->id
        );

        $this->response->assertStatus(404);
    }
}
