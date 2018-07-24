<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnbilledGrvGroupByApiTest extends TestCase
{
    use MakeUnbilledGrvGroupByTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUnbilledGrvGroupBy()
    {
        $unbilledGrvGroupBy = $this->fakeUnbilledGrvGroupByData();
        $this->json('POST', '/api/v1/unbilledGrvGroupBies', $unbilledGrvGroupBy);

        $this->assertApiResponse($unbilledGrvGroupBy);
    }

    /**
     * @test
     */
    public function testReadUnbilledGrvGroupBy()
    {
        $unbilledGrvGroupBy = $this->makeUnbilledGrvGroupBy();
        $this->json('GET', '/api/v1/unbilledGrvGroupBies/'.$unbilledGrvGroupBy->id);

        $this->assertApiResponse($unbilledGrvGroupBy->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUnbilledGrvGroupBy()
    {
        $unbilledGrvGroupBy = $this->makeUnbilledGrvGroupBy();
        $editedUnbilledGrvGroupBy = $this->fakeUnbilledGrvGroupByData();

        $this->json('PUT', '/api/v1/unbilledGrvGroupBies/'.$unbilledGrvGroupBy->id, $editedUnbilledGrvGroupBy);

        $this->assertApiResponse($editedUnbilledGrvGroupBy);
    }

    /**
     * @test
     */
    public function testDeleteUnbilledGrvGroupBy()
    {
        $unbilledGrvGroupBy = $this->makeUnbilledGrvGroupBy();
        $this->json('DELETE', '/api/v1/unbilledGrvGroupBies/'.$unbilledGrvGroupBy->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/unbilledGrvGroupBies/'.$unbilledGrvGroupBy->id);

        $this->assertResponseStatus(404);
    }
}
