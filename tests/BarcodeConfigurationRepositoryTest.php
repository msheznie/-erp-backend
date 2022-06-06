<?php namespace Tests\Repositories;

use App\Models\BarcodeConfiguration;
use App\Repositories\BarcodeConfigurationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BarcodeConfigurationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BarcodeConfigurationRepository
     */
    protected $barcodeConfigurationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->barcodeConfigurationRepo = \App::make(BarcodeConfigurationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_barcode_configuration()
    {
        $barcodeConfiguration = factory(BarcodeConfiguration::class)->make()->toArray();

        $createdBarcodeConfiguration = $this->barcodeConfigurationRepo->create($barcodeConfiguration);

        $createdBarcodeConfiguration = $createdBarcodeConfiguration->toArray();
        $this->assertArrayHasKey('id', $createdBarcodeConfiguration);
        $this->assertNotNull($createdBarcodeConfiguration['id'], 'Created BarcodeConfiguration must have id specified');
        $this->assertNotNull(BarcodeConfiguration::find($createdBarcodeConfiguration['id']), 'BarcodeConfiguration with given id must be in DB');
        $this->assertModelData($barcodeConfiguration, $createdBarcodeConfiguration);
    }

    /**
     * @test read
     */
    public function test_read_barcode_configuration()
    {
        $barcodeConfiguration = factory(BarcodeConfiguration::class)->create();

        $dbBarcodeConfiguration = $this->barcodeConfigurationRepo->find($barcodeConfiguration->id);

        $dbBarcodeConfiguration = $dbBarcodeConfiguration->toArray();
        $this->assertModelData($barcodeConfiguration->toArray(), $dbBarcodeConfiguration);
    }

    /**
     * @test update
     */
    public function test_update_barcode_configuration()
    {
        $barcodeConfiguration = factory(BarcodeConfiguration::class)->create();
        $fakeBarcodeConfiguration = factory(BarcodeConfiguration::class)->make()->toArray();

        $updatedBarcodeConfiguration = $this->barcodeConfigurationRepo->update($fakeBarcodeConfiguration, $barcodeConfiguration->id);

        $this->assertModelData($fakeBarcodeConfiguration, $updatedBarcodeConfiguration->toArray());
        $dbBarcodeConfiguration = $this->barcodeConfigurationRepo->find($barcodeConfiguration->id);
        $this->assertModelData($fakeBarcodeConfiguration, $dbBarcodeConfiguration->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_barcode_configuration()
    {
        $barcodeConfiguration = factory(BarcodeConfiguration::class)->create();

        $resp = $this->barcodeConfigurationRepo->delete($barcodeConfiguration->id);

        $this->assertTrue($resp);
        $this->assertNull(BarcodeConfiguration::find($barcodeConfiguration->id), 'BarcodeConfiguration should not exist in DB');
    }
}
