<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetInsuranceDetailApiTest extends TestCase
{
    use MakeFixedAssetInsuranceDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFixedAssetInsuranceDetail()
    {
        $fixedAssetInsuranceDetail = $this->fakeFixedAssetInsuranceDetailData();
        $this->json('POST', '/api/v1/fixedAssetInsuranceDetails', $fixedAssetInsuranceDetail);

        $this->assertApiResponse($fixedAssetInsuranceDetail);
    }

    /**
     * @test
     */
    public function testReadFixedAssetInsuranceDetail()
    {
        $fixedAssetInsuranceDetail = $this->makeFixedAssetInsuranceDetail();
        $this->json('GET', '/api/v1/fixedAssetInsuranceDetails/'.$fixedAssetInsuranceDetail->id);

        $this->assertApiResponse($fixedAssetInsuranceDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFixedAssetInsuranceDetail()
    {
        $fixedAssetInsuranceDetail = $this->makeFixedAssetInsuranceDetail();
        $editedFixedAssetInsuranceDetail = $this->fakeFixedAssetInsuranceDetailData();

        $this->json('PUT', '/api/v1/fixedAssetInsuranceDetails/'.$fixedAssetInsuranceDetail->id, $editedFixedAssetInsuranceDetail);

        $this->assertApiResponse($editedFixedAssetInsuranceDetail);
    }

    /**
     * @test
     */
    public function testDeleteFixedAssetInsuranceDetail()
    {
        $fixedAssetInsuranceDetail = $this->makeFixedAssetInsuranceDetail();
        $this->json('DELETE', '/api/v1/fixedAssetInsuranceDetails/'.$fixedAssetInsuranceDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fixedAssetInsuranceDetails/'.$fixedAssetInsuranceDetail->id);

        $this->assertResponseStatus(404);
    }
}
