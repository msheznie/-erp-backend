<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EliminationLedger;

class EliminationLedgerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_elimination_ledger()
    {
        $eliminationLedger = factory(EliminationLedger::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/elimination_ledgers', $eliminationLedger
        );

        $this->assertApiResponse($eliminationLedger);
    }

    /**
     * @test
     */
    public function test_read_elimination_ledger()
    {
        $eliminationLedger = factory(EliminationLedger::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/elimination_ledgers/'.$eliminationLedger->id
        );

        $this->assertApiResponse($eliminationLedger->toArray());
    }

    /**
     * @test
     */
    public function test_update_elimination_ledger()
    {
        $eliminationLedger = factory(EliminationLedger::class)->create();
        $editedEliminationLedger = factory(EliminationLedger::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/elimination_ledgers/'.$eliminationLedger->id,
            $editedEliminationLedger
        );

        $this->assertApiResponse($editedEliminationLedger);
    }

    /**
     * @test
     */
    public function test_delete_elimination_ledger()
    {
        $eliminationLedger = factory(EliminationLedger::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/elimination_ledgers/'.$eliminationLedger->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/elimination_ledgers/'.$eliminationLedger->id
        );

        $this->response->assertStatus(404);
    }
}
