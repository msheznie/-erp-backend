<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OutletUsersApiTest extends TestCase
{
    use MakeOutletUsersTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateOutletUsers()
    {
        $outletUsers = $this->fakeOutletUsersData();
        $this->json('POST', '/api/v1/outletUsers', $outletUsers);

        $this->assertApiResponse($outletUsers);
    }

    /**
     * @test
     */
    public function testReadOutletUsers()
    {
        $outletUsers = $this->makeOutletUsers();
        $this->json('GET', '/api/v1/outletUsers/'.$outletUsers->id);

        $this->assertApiResponse($outletUsers->toArray());
    }

    /**
     * @test
     */
    public function testUpdateOutletUsers()
    {
        $outletUsers = $this->makeOutletUsers();
        $editedOutletUsers = $this->fakeOutletUsersData();

        $this->json('PUT', '/api/v1/outletUsers/'.$outletUsers->id, $editedOutletUsers);

        $this->assertApiResponse($editedOutletUsers);
    }

    /**
     * @test
     */
    public function testDeleteOutletUsers()
    {
        $outletUsers = $this->makeOutletUsers();
        $this->json('DELETE', '/api/v1/outletUsers/'.$outletUsers->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/outletUsers/'.$outletUsers->id);

        $this->assertResponseStatus(404);
    }
}
