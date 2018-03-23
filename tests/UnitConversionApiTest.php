<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitConversionApiTest extends TestCase
{
    use MakeUnitConversionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUnitConversion()
    {
        $unitConversion = $this->fakeUnitConversionData();
        $this->json('POST', '/api/v1/unitConversions', $unitConversion);

        $this->assertApiResponse($unitConversion);
    }

    /**
     * @test
     */
    public function testReadUnitConversion()
    {
        $unitConversion = $this->makeUnitConversion();
        $this->json('GET', '/api/v1/unitConversions/'.$unitConversion->id);

        $this->assertApiResponse($unitConversion->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUnitConversion()
    {
        $unitConversion = $this->makeUnitConversion();
        $editedUnitConversion = $this->fakeUnitConversionData();

        $this->json('PUT', '/api/v1/unitConversions/'.$unitConversion->id, $editedUnitConversion);

        $this->assertApiResponse($editedUnitConversion);
    }

    /**
     * @test
     */
    public function testDeleteUnitConversion()
    {
        $unitConversion = $this->makeUnitConversion();
        $this->json('DELETE', '/api/v1/unitConversions/'.$unitConversion->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/unitConversions/'.$unitConversion->id);

        $this->assertResponseStatus(404);
    }
}
