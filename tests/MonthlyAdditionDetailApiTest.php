<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MonthlyAdditionDetailApiTest extends TestCase
{
    use MakeMonthlyAdditionDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMonthlyAdditionDetail()
    {
        $monthlyAdditionDetail = $this->fakeMonthlyAdditionDetailData();
        $this->json('POST', '/api/v1/monthlyAdditionDetails', $monthlyAdditionDetail);

        $this->assertApiResponse($monthlyAdditionDetail);
    }

    /**
     * @test
     */
    public function testReadMonthlyAdditionDetail()
    {
        $monthlyAdditionDetail = $this->makeMonthlyAdditionDetail();
        $this->json('GET', '/api/v1/monthlyAdditionDetails/'.$monthlyAdditionDetail->id);

        $this->assertApiResponse($monthlyAdditionDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMonthlyAdditionDetail()
    {
        $monthlyAdditionDetail = $this->makeMonthlyAdditionDetail();
        $editedMonthlyAdditionDetail = $this->fakeMonthlyAdditionDetailData();

        $this->json('PUT', '/api/v1/monthlyAdditionDetails/'.$monthlyAdditionDetail->id, $editedMonthlyAdditionDetail);

        $this->assertApiResponse($editedMonthlyAdditionDetail);
    }

    /**
     * @test
     */
    public function testDeleteMonthlyAdditionDetail()
    {
        $monthlyAdditionDetail = $this->makeMonthlyAdditionDetail();
        $this->json('DELETE', '/api/v1/monthlyAdditionDetails/'.$monthlyAdditionDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/monthlyAdditionDetails/'.$monthlyAdditionDetail->id);

        $this->assertResponseStatus(404);
    }
}
