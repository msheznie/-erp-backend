<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class YesNoSelectionForMinusApiTest extends TestCase
{
    use MakeYesNoSelectionForMinusTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateYesNoSelectionForMinus()
    {
        $yesNoSelectionForMinus = $this->fakeYesNoSelectionForMinusData();
        $this->json('POST', '/api/v1/yesNoSelectionForMinuses', $yesNoSelectionForMinus);

        $this->assertApiResponse($yesNoSelectionForMinus);
    }

    /**
     * @test
     */
    public function testReadYesNoSelectionForMinus()
    {
        $yesNoSelectionForMinus = $this->makeYesNoSelectionForMinus();
        $this->json('GET', '/api/v1/yesNoSelectionForMinuses/'.$yesNoSelectionForMinus->id);

        $this->assertApiResponse($yesNoSelectionForMinus->toArray());
    }

    /**
     * @test
     */
    public function testUpdateYesNoSelectionForMinus()
    {
        $yesNoSelectionForMinus = $this->makeYesNoSelectionForMinus();
        $editedYesNoSelectionForMinus = $this->fakeYesNoSelectionForMinusData();

        $this->json('PUT', '/api/v1/yesNoSelectionForMinuses/'.$yesNoSelectionForMinus->id, $editedYesNoSelectionForMinus);

        $this->assertApiResponse($editedYesNoSelectionForMinus);
    }

    /**
     * @test
     */
    public function testDeleteYesNoSelectionForMinus()
    {
        $yesNoSelectionForMinus = $this->makeYesNoSelectionForMinus();
        $this->json('DELETE', '/api/v1/yesNoSelectionForMinuses/'.$yesNoSelectionForMinus->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/yesNoSelectionForMinuses/'.$yesNoSelectionForMinus->id);

        $this->assertResponseStatus(404);
    }
}
