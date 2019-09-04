<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCalenderMasterTrait;
use Tests\ApiTestTrait;

class CalenderMasterApiTest extends TestCase
{
    use MakeCalenderMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_calender_master()
    {
        $calenderMaster = $this->fakeCalenderMasterData();
        $this->response = $this->json('POST', '/api/calenderMasters', $calenderMaster);

        $this->assertApiResponse($calenderMaster);
    }

    /**
     * @test
     */
    public function test_read_calender_master()
    {
        $calenderMaster = $this->makeCalenderMaster();
        $this->response = $this->json('GET', '/api/calenderMasters/'.$calenderMaster->id);

        $this->assertApiResponse($calenderMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_calender_master()
    {
        $calenderMaster = $this->makeCalenderMaster();
        $editedCalenderMaster = $this->fakeCalenderMasterData();

        $this->response = $this->json('PUT', '/api/calenderMasters/'.$calenderMaster->id, $editedCalenderMaster);

        $this->assertApiResponse($editedCalenderMaster);
    }

    /**
     * @test
     */
    public function test_delete_calender_master()
    {
        $calenderMaster = $this->makeCalenderMaster();
        $this->response = $this->json('DELETE', '/api/calenderMasters/'.$calenderMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/calenderMasters/'.$calenderMaster->id);

        $this->response->assertStatus(404);
    }
}
