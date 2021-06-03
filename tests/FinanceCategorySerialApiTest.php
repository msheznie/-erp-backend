<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinanceCategorySerial;

class FinanceCategorySerialApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_finance_category_serial()
    {
        $financeCategorySerial = factory(FinanceCategorySerial::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/finance_category_serials', $financeCategorySerial
        );

        $this->assertApiResponse($financeCategorySerial);
    }

    /**
     * @test
     */
    public function test_read_finance_category_serial()
    {
        $financeCategorySerial = factory(FinanceCategorySerial::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/finance_category_serials/'.$financeCategorySerial->id
        );

        $this->assertApiResponse($financeCategorySerial->toArray());
    }

    /**
     * @test
     */
    public function test_update_finance_category_serial()
    {
        $financeCategorySerial = factory(FinanceCategorySerial::class)->create();
        $editedFinanceCategorySerial = factory(FinanceCategorySerial::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/finance_category_serials/'.$financeCategorySerial->id,
            $editedFinanceCategorySerial
        );

        $this->assertApiResponse($editedFinanceCategorySerial);
    }

    /**
     * @test
     */
    public function test_delete_finance_category_serial()
    {
        $financeCategorySerial = factory(FinanceCategorySerial::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/finance_category_serials/'.$financeCategorySerial->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/finance_category_serials/'.$financeCategorySerial->id
        );

        $this->response->assertStatus(404);
    }
}
