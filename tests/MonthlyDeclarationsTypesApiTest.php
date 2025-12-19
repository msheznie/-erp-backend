<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MonthlyDeclarationsTypes;

class MonthlyDeclarationsTypesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_monthly_declarations_types()
    {
        $monthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/monthly_declarations_types', $monthlyDeclarationsTypes
        );

        $this->assertApiResponse($monthlyDeclarationsTypes);
    }

    /**
     * @test
     */
    public function test_read_monthly_declarations_types()
    {
        $monthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/monthly_declarations_types/'.$monthlyDeclarationsTypes->id
        );

        $this->assertApiResponse($monthlyDeclarationsTypes->toArray());
    }

    /**
     * @test
     */
    public function test_update_monthly_declarations_types()
    {
        $monthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->create();
        $editedMonthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/monthly_declarations_types/'.$monthlyDeclarationsTypes->id,
            $editedMonthlyDeclarationsTypes
        );

        $this->assertApiResponse($editedMonthlyDeclarationsTypes);
    }

    /**
     * @test
     */
    public function test_delete_monthly_declarations_types()
    {
        $monthlyDeclarationsTypes = factory(MonthlyDeclarationsTypes::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/monthly_declarations_types/'.$monthlyDeclarationsTypes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/monthly_declarations_types/'.$monthlyDeclarationsTypes->id
        );

        $this->response->assertStatus(404);
    }
}
