<?php namespace Tests\Repositories;

use App\Models\SystemGlCodeScenarioDetail;
use App\Repositories\SystemGlCodeScenarioDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SystemGlCodeScenarioDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SystemGlCodeScenarioDetailRepository
     */
    protected $systemGlCodeScenarioDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->systemGlCodeScenarioDetailRepo = \App::make(SystemGlCodeScenarioDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_system_gl_code_scenario_detail()
    {
        $systemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->make()->toArray();

        $createdSystemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepo->create($systemGlCodeScenarioDetail);

        $createdSystemGlCodeScenarioDetail = $createdSystemGlCodeScenarioDetail->toArray();
        $this->assertArrayHasKey('id', $createdSystemGlCodeScenarioDetail);
        $this->assertNotNull($createdSystemGlCodeScenarioDetail['id'], 'Created SystemGlCodeScenarioDetail must have id specified');
        $this->assertNotNull(SystemGlCodeScenarioDetail::find($createdSystemGlCodeScenarioDetail['id']), 'SystemGlCodeScenarioDetail with given id must be in DB');
        $this->assertModelData($systemGlCodeScenarioDetail, $createdSystemGlCodeScenarioDetail);
    }

    /**
     * @test read
     */
    public function test_read_system_gl_code_scenario_detail()
    {
        $systemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->create();

        $dbSystemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepo->find($systemGlCodeScenarioDetail->id);

        $dbSystemGlCodeScenarioDetail = $dbSystemGlCodeScenarioDetail->toArray();
        $this->assertModelData($systemGlCodeScenarioDetail->toArray(), $dbSystemGlCodeScenarioDetail);
    }

    /**
     * @test update
     */
    public function test_update_system_gl_code_scenario_detail()
    {
        $systemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->create();
        $fakeSystemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->make()->toArray();

        $updatedSystemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepo->update($fakeSystemGlCodeScenarioDetail, $systemGlCodeScenarioDetail->id);

        $this->assertModelData($fakeSystemGlCodeScenarioDetail, $updatedSystemGlCodeScenarioDetail->toArray());
        $dbSystemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepo->find($systemGlCodeScenarioDetail->id);
        $this->assertModelData($fakeSystemGlCodeScenarioDetail, $dbSystemGlCodeScenarioDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_system_gl_code_scenario_detail()
    {
        $systemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->create();

        $resp = $this->systemGlCodeScenarioDetailRepo->delete($systemGlCodeScenarioDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(SystemGlCodeScenarioDetail::find($systemGlCodeScenarioDetail->id), 'SystemGlCodeScenarioDetail should not exist in DB');
    }
}
