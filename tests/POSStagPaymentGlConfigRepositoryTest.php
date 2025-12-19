<?php namespace Tests\Repositories;

use App\Models\POSStagPaymentGlConfig;
use App\Repositories\POSStagPaymentGlConfigRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSStagPaymentGlConfigRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSStagPaymentGlConfigRepository
     */
    protected $pOSStagPaymentGlConfigRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSStagPaymentGlConfigRepo = \App::make(POSStagPaymentGlConfigRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_stag_payment_gl_config()
    {
        $pOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->make()->toArray();

        $createdPOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepo->create($pOSStagPaymentGlConfig);

        $createdPOSStagPaymentGlConfig = $createdPOSStagPaymentGlConfig->toArray();
        $this->assertArrayHasKey('id', $createdPOSStagPaymentGlConfig);
        $this->assertNotNull($createdPOSStagPaymentGlConfig['id'], 'Created POSStagPaymentGlConfig must have id specified');
        $this->assertNotNull(POSStagPaymentGlConfig::find($createdPOSStagPaymentGlConfig['id']), 'POSStagPaymentGlConfig with given id must be in DB');
        $this->assertModelData($pOSStagPaymentGlConfig, $createdPOSStagPaymentGlConfig);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_stag_payment_gl_config()
    {
        $pOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->create();

        $dbPOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepo->find($pOSStagPaymentGlConfig->id);

        $dbPOSStagPaymentGlConfig = $dbPOSStagPaymentGlConfig->toArray();
        $this->assertModelData($pOSStagPaymentGlConfig->toArray(), $dbPOSStagPaymentGlConfig);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_stag_payment_gl_config()
    {
        $pOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->create();
        $fakePOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->make()->toArray();

        $updatedPOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepo->update($fakePOSStagPaymentGlConfig, $pOSStagPaymentGlConfig->id);

        $this->assertModelData($fakePOSStagPaymentGlConfig, $updatedPOSStagPaymentGlConfig->toArray());
        $dbPOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepo->find($pOSStagPaymentGlConfig->id);
        $this->assertModelData($fakePOSStagPaymentGlConfig, $dbPOSStagPaymentGlConfig->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_stag_payment_gl_config()
    {
        $pOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->create();

        $resp = $this->pOSStagPaymentGlConfigRepo->delete($pOSStagPaymentGlConfig->id);

        $this->assertTrue($resp);
        $this->assertNull(POSStagPaymentGlConfig::find($pOSStagPaymentGlConfig->id), 'POSStagPaymentGlConfig should not exist in DB');
    }
}
