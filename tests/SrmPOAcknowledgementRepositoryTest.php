<?php namespace Tests\Repositories;

use App\Models\SrmPOAcknowledgement;
use App\Repositories\SrmPOAcknowledgementRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrmPOAcknowledgementRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrmPOAcknowledgementRepository
     */
    protected $srmPOAcknowledgementRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srmPOAcknowledgementRepo = \App::make(SrmPOAcknowledgementRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srm_p_o_acknowledgement()
    {
        $srmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->make()->toArray();

        $createdSrmPOAcknowledgement = $this->srmPOAcknowledgementRepo->create($srmPOAcknowledgement);

        $createdSrmPOAcknowledgement = $createdSrmPOAcknowledgement->toArray();
        $this->assertArrayHasKey('id', $createdSrmPOAcknowledgement);
        $this->assertNotNull($createdSrmPOAcknowledgement['id'], 'Created SrmPOAcknowledgement must have id specified');
        $this->assertNotNull(SrmPOAcknowledgement::find($createdSrmPOAcknowledgement['id']), 'SrmPOAcknowledgement with given id must be in DB');
        $this->assertModelData($srmPOAcknowledgement, $createdSrmPOAcknowledgement);
    }

    /**
     * @test read
     */
    public function test_read_srm_p_o_acknowledgement()
    {
        $srmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->create();

        $dbSrmPOAcknowledgement = $this->srmPOAcknowledgementRepo->find($srmPOAcknowledgement->id);

        $dbSrmPOAcknowledgement = $dbSrmPOAcknowledgement->toArray();
        $this->assertModelData($srmPOAcknowledgement->toArray(), $dbSrmPOAcknowledgement);
    }

    /**
     * @test update
     */
    public function test_update_srm_p_o_acknowledgement()
    {
        $srmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->create();
        $fakeSrmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->make()->toArray();

        $updatedSrmPOAcknowledgement = $this->srmPOAcknowledgementRepo->update($fakeSrmPOAcknowledgement, $srmPOAcknowledgement->id);

        $this->assertModelData($fakeSrmPOAcknowledgement, $updatedSrmPOAcknowledgement->toArray());
        $dbSrmPOAcknowledgement = $this->srmPOAcknowledgementRepo->find($srmPOAcknowledgement->id);
        $this->assertModelData($fakeSrmPOAcknowledgement, $dbSrmPOAcknowledgement->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srm_p_o_acknowledgement()
    {
        $srmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->create();

        $resp = $this->srmPOAcknowledgementRepo->delete($srmPOAcknowledgement->id);

        $this->assertTrue($resp);
        $this->assertNull(SrmPOAcknowledgement::find($srmPOAcknowledgement->id), 'SrmPOAcknowledgement should not exist in DB');
    }
}
