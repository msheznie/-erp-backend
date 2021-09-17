<?php namespace Tests\Repositories;

use App\Models\SrpErpTemplates;
use App\Repositories\SrpErpTemplatesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrpErpTemplatesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrpErpTemplatesRepository
     */
    protected $srpErpTemplatesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srpErpTemplatesRepo = \App::make(SrpErpTemplatesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srp_erp_templates()
    {
        $srpErpTemplates = factory(SrpErpTemplates::class)->make()->toArray();

        $createdSrpErpTemplates = $this->srpErpTemplatesRepo->create($srpErpTemplates);

        $createdSrpErpTemplates = $createdSrpErpTemplates->toArray();
        $this->assertArrayHasKey('id', $createdSrpErpTemplates);
        $this->assertNotNull($createdSrpErpTemplates['id'], 'Created SrpErpTemplates must have id specified');
        $this->assertNotNull(SrpErpTemplates::find($createdSrpErpTemplates['id']), 'SrpErpTemplates with given id must be in DB');
        $this->assertModelData($srpErpTemplates, $createdSrpErpTemplates);
    }

    /**
     * @test read
     */
    public function test_read_srp_erp_templates()
    {
        $srpErpTemplates = factory(SrpErpTemplates::class)->create();

        $dbSrpErpTemplates = $this->srpErpTemplatesRepo->find($srpErpTemplates->id);

        $dbSrpErpTemplates = $dbSrpErpTemplates->toArray();
        $this->assertModelData($srpErpTemplates->toArray(), $dbSrpErpTemplates);
    }

    /**
     * @test update
     */
    public function test_update_srp_erp_templates()
    {
        $srpErpTemplates = factory(SrpErpTemplates::class)->create();
        $fakeSrpErpTemplates = factory(SrpErpTemplates::class)->make()->toArray();

        $updatedSrpErpTemplates = $this->srpErpTemplatesRepo->update($fakeSrpErpTemplates, $srpErpTemplates->id);

        $this->assertModelData($fakeSrpErpTemplates, $updatedSrpErpTemplates->toArray());
        $dbSrpErpTemplates = $this->srpErpTemplatesRepo->find($srpErpTemplates->id);
        $this->assertModelData($fakeSrpErpTemplates, $dbSrpErpTemplates->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srp_erp_templates()
    {
        $srpErpTemplates = factory(SrpErpTemplates::class)->create();

        $resp = $this->srpErpTemplatesRepo->delete($srpErpTemplates->id);

        $this->assertTrue($resp);
        $this->assertNull(SrpErpTemplates::find($srpErpTemplates->id), 'SrpErpTemplates should not exist in DB');
    }
}
