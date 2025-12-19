<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PosStagMenuMaster;

class PosStagMenuMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pos_stag_menu_master()
    {
        $posStagMenuMaster = factory(PosStagMenuMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pos_stag_menu_masters', $posStagMenuMaster
        );

        $this->assertApiResponse($posStagMenuMaster);
    }

    /**
     * @test
     */
    public function test_read_pos_stag_menu_master()
    {
        $posStagMenuMaster = factory(PosStagMenuMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pos_stag_menu_masters/'.$posStagMenuMaster->id
        );

        $this->assertApiResponse($posStagMenuMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_pos_stag_menu_master()
    {
        $posStagMenuMaster = factory(PosStagMenuMaster::class)->create();
        $editedPosStagMenuMaster = factory(PosStagMenuMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pos_stag_menu_masters/'.$posStagMenuMaster->id,
            $editedPosStagMenuMaster
        );

        $this->assertApiResponse($editedPosStagMenuMaster);
    }

    /**
     * @test
     */
    public function test_delete_pos_stag_menu_master()
    {
        $posStagMenuMaster = factory(PosStagMenuMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pos_stag_menu_masters/'.$posStagMenuMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pos_stag_menu_masters/'.$posStagMenuMaster->id
        );

        $this->response->assertStatus(404);
    }
}
