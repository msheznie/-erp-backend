<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class YesNoSelectionApiTest extends TestCase
{
    use MakeYesNoSelectionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateYesNoSelection()
    {
        $yesNoSelection = $this->fakeYesNoSelectionData();
        $this->json('POST', '/api/v1/yesNoSelections', $yesNoSelection);

        $this->assertApiResponse($yesNoSelection);
    }

    /**
     * @test
     */
    public function testReadYesNoSelection()
    {
        $yesNoSelection = $this->makeYesNoSelection();
        $this->json('GET', '/api/v1/yesNoSelections/'.$yesNoSelection->id);

        $this->assertApiResponse($yesNoSelection->toArray());
    }

    /**
     * @test
     */
    public function testUpdateYesNoSelection()
    {
        $yesNoSelection = $this->makeYesNoSelection();
        $editedYesNoSelection = $this->fakeYesNoSelectionData();

        $this->json('PUT', '/api/v1/yesNoSelections/'.$yesNoSelection->id, $editedYesNoSelection);

        $this->assertApiResponse($editedYesNoSelection);
    }

    /**
     * @test
     */
    public function testDeleteYesNoSelection()
    {
        $yesNoSelection = $this->makeYesNoSelection();
        $this->json('DELETE', '/api/v1/yesNoSelections/'.$yesNoSelection->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/yesNoSelections/'.$yesNoSelection->id);

        $this->assertResponseStatus(404);
    }
}
