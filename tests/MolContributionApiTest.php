<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MolContribution;

class MolContributionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mol_contribution()
    {
        $molContribution = factory(MolContribution::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mol_contributions', $molContribution
        );

        $this->assertApiResponse($molContribution);
    }

    /**
     * @test
     */
    public function test_read_mol_contribution()
    {
        $molContribution = factory(MolContribution::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/mol_contributions/'.$molContribution->id
        );

        $this->assertApiResponse($molContribution->toArray());
    }

    /**
     * @test
     */
    public function test_update_mol_contribution()
    {
        $molContribution = factory(MolContribution::class)->create();
        $editedMolContribution = factory(MolContribution::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mol_contributions/'.$molContribution->id,
            $editedMolContribution
        );

        $this->assertApiResponse($editedMolContribution);
    }

    /**
     * @test
     */
    public function test_delete_mol_contribution()
    {
        $molContribution = factory(MolContribution::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mol_contributions/'.$molContribution->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mol_contributions/'.$molContribution->id
        );

        $this->response->assertStatus(404);
    }
}
