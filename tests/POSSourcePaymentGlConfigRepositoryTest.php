<?php namespace Tests\Repositories;

use App\Models\POSSourcePaymentGlConfig;
use App\Repositories\POSSourcePaymentGlConfigRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourcePaymentGlConfigRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourcePaymentGlConfigRepository
     */
    protected $pOSSourcePaymentGlConfigRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourcePaymentGlConfigRepo = \App::make(POSSourcePaymentGlConfigRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_payment_gl_config()
    {
        $pOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->make()->toArray();

        $createdPOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepo->create($pOSSourcePaymentGlConfig);

        $createdPOSSourcePaymentGlConfig = $createdPOSSourcePaymentGlConfig->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourcePaymentGlConfig);
        $this->assertNotNull($createdPOSSourcePaymentGlConfig['id'], 'Created POSSourcePaymentGlConfig must have id specified');
        $this->assertNotNull(POSSourcePaymentGlConfig::find($createdPOSSourcePaymentGlConfig['id']), 'POSSourcePaymentGlConfig with given id must be in DB');
        $this->assertModelData($pOSSourcePaymentGlConfig, $createdPOSSourcePaymentGlConfig);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_payment_gl_config()
    {
        $pOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->create();

        $dbPOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepo->find($pOSSourcePaymentGlConfig->id);

        $dbPOSSourcePaymentGlConfig = $dbPOSSourcePaymentGlConfig->toArray();
        $this->assertModelData($pOSSourcePaymentGlConfig->toArray(), $dbPOSSourcePaymentGlConfig);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_payment_gl_config()
    {
        $pOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->create();
        $fakePOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->make()->toArray();

        $updatedPOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepo->update($fakePOSSourcePaymentGlConfig, $pOSSourcePaymentGlConfig->id);

        $this->assertModelData($fakePOSSourcePaymentGlConfig, $updatedPOSSourcePaymentGlConfig->toArray());
        $dbPOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepo->find($pOSSourcePaymentGlConfig->id);
        $this->assertModelData($fakePOSSourcePaymentGlConfig, $dbPOSSourcePaymentGlConfig->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_payment_gl_config()
    {
        $pOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->create();

        $resp = $this->pOSSourcePaymentGlConfigRepo->delete($pOSSourcePaymentGlConfig->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourcePaymentGlConfig::find($pOSSourcePaymentGlConfig->id), 'POSSourcePaymentGlConfig should not exist in DB');
    }
}
