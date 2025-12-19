<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HodAction;

class HodActionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hod_action()
    {
        $hodAction = factory(HodAction::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hod_actions', $hodAction
        );

        $this->assertApiResponse($hodAction);
    }

    /**
     * @test
     */
    public function test_read_hod_action()
    {
        $hodAction = factory(HodAction::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hod_actions/'.$hodAction->id
        );

        $this->assertApiResponse($hodAction->toArray());
    }

    /**
     * @test
     */
    public function test_update_hod_action()
    {
        $hodAction = factory(HodAction::class)->create();
        $editedHodAction = factory(HodAction::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hod_actions/'.$hodAction->id,
            $editedHodAction
        );

        $this->assertApiResponse($editedHodAction);
    }

    /**
     * @test
     */
    public function test_delete_hod_action()
    {
        $hodAction = factory(HodAction::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hod_actions/'.$hodAction->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hod_actions/'.$hodAction->id
        );

        $this->response->assertStatus(404);
    }
}
