<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PosSourceMenuMaster;

class PosSourceMenuMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pos_source_menu_master()
    {
        $posSourceMenuMaster = factory(PosSourceMenuMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pos_source_menu_masters', $posSourceMenuMaster
        );

        $this->assertApiResponse($posSourceMenuMaster);
    }

    /**
     * @test
     */
    public function test_read_pos_source_menu_master()
    {
        $posSourceMenuMaster = factory(PosSourceMenuMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pos_source_menu_masters/'.$posSourceMenuMaster->id
        );

        $this->assertApiResponse($posSourceMenuMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_pos_source_menu_master()
    {
        $posSourceMenuMaster = factory(PosSourceMenuMaster::class)->create();
        $editedPosSourceMenuMaster = factory(PosSourceMenuMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pos_source_menu_masters/'.$posSourceMenuMaster->id,
            $editedPosSourceMenuMaster
        );

        $this->assertApiResponse($editedPosSourceMenuMaster);
    }

    /**
     * @test
     */
    public function test_delete_pos_source_menu_master()
    {
        $posSourceMenuMaster = factory(PosSourceMenuMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pos_source_menu_masters/'.$posSourceMenuMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pos_source_menu_masters/'.$posSourceMenuMaster->id
        );

        $this->response->assertStatus(404);
    }
}
