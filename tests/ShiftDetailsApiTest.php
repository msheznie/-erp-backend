<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShiftDetailsApiTest extends TestCase
{
    use MakeShiftDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateShiftDetails()
    {
        $shiftDetails = $this->fakeShiftDetailsData();
        $this->json('POST', '/api/v1/shiftDetails', $shiftDetails);

        $this->assertApiResponse($shiftDetails);
    }

    /**
     * @test
     */
    public function testReadShiftDetails()
    {
        $shiftDetails = $this->makeShiftDetails();
        $this->json('GET', '/api/v1/shiftDetails/'.$shiftDetails->id);

        $this->assertApiResponse($shiftDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateShiftDetails()
    {
        $shiftDetails = $this->makeShiftDetails();
        $editedShiftDetails = $this->fakeShiftDetailsData();

        $this->json('PUT', '/api/v1/shiftDetails/'.$shiftDetails->id, $editedShiftDetails);

        $this->assertApiResponse($editedShiftDetails);
    }

    /**
     * @test
     */
    public function testDeleteShiftDetails()
    {
        $shiftDetails = $this->makeShiftDetails();
        $this->json('DELETE', '/api/v1/shiftDetails/'.$shiftDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/shiftDetails/'.$shiftDetails->id);

        $this->assertResponseStatus(404);
    }
}
