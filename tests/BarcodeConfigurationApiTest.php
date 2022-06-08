<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BarcodeConfiguration;

class BarcodeConfigurationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_barcode_configuration()
    {
        $barcodeConfiguration = factory(BarcodeConfiguration::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/barcode_configurations', $barcodeConfiguration
        );

        $this->assertApiResponse($barcodeConfiguration);
    }

    /**
     * @test
     */
    public function test_read_barcode_configuration()
    {
        $barcodeConfiguration = factory(BarcodeConfiguration::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/barcode_configurations/'.$barcodeConfiguration->id
        );

        $this->assertApiResponse($barcodeConfiguration->toArray());
    }

    /**
     * @test
     */
    public function test_update_barcode_configuration()
    {
        $barcodeConfiguration = factory(BarcodeConfiguration::class)->create();
        $editedBarcodeConfiguration = factory(BarcodeConfiguration::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/barcode_configurations/'.$barcodeConfiguration->id,
            $editedBarcodeConfiguration
        );

        $this->assertApiResponse($editedBarcodeConfiguration);
    }

    /**
     * @test
     */
    public function test_delete_barcode_configuration()
    {
        $barcodeConfiguration = factory(BarcodeConfiguration::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/barcode_configurations/'.$barcodeConfiguration->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/barcode_configurations/'.$barcodeConfiguration->id
        );

        $this->response->assertStatus(404);
    }
}
