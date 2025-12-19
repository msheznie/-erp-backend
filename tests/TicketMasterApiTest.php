<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketMasterApiTest extends TestCase
{
    use MakeTicketMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTicketMaster()
    {
        $ticketMaster = $this->fakeTicketMasterData();
        $this->json('POST', '/api/v1/ticketMasters', $ticketMaster);

        $this->assertApiResponse($ticketMaster);
    }

    /**
     * @test
     */
    public function testReadTicketMaster()
    {
        $ticketMaster = $this->makeTicketMaster();
        $this->json('GET', '/api/v1/ticketMasters/'.$ticketMaster->id);

        $this->assertApiResponse($ticketMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTicketMaster()
    {
        $ticketMaster = $this->makeTicketMaster();
        $editedTicketMaster = $this->fakeTicketMasterData();

        $this->json('PUT', '/api/v1/ticketMasters/'.$ticketMaster->id, $editedTicketMaster);

        $this->assertApiResponse($editedTicketMaster);
    }

    /**
     * @test
     */
    public function testDeleteTicketMaster()
    {
        $ticketMaster = $this->makeTicketMaster();
        $this->json('DELETE', '/api/v1/ticketMasters/'.$ticketMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/ticketMasters/'.$ticketMaster->id);

        $this->assertResponseStatus(404);
    }
}
