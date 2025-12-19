<?php namespace Tests\Repositories;

use App\Models\SystemGlCodeScenario;
use App\Repositories\SystemGlCodeScenarioRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SystemGlCodeScenarioRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SystemGlCodeScenarioRepository
     */
    protected $systemGlCodeScenarioRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->systemGlCodeScenarioRepo = \App::make(SystemGlCodeScenarioRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_system_gl_code_scenario()
    {
        $systemGlCodeScenario = factory(SystemGlCodeScenario::class)->make()->toArray();

        $createdSystemGlCodeScenario = $this->systemGlCodeScenarioRepo->create($systemGlCodeScenario);

        $createdSystemGlCodeScenario = $createdSystemGlCodeScenario->toArray();
        $this->assertArrayHasKey('id', $createdSystemGlCodeScenario);
        $this->assertNotNull($createdSystemGlCodeScenario['id'], 'Created SystemGlCodeScenario must have id specified');
        $this->assertNotNull(SystemGlCodeScenario::find($createdSystemGlCodeScenario['id']), 'SystemGlCodeScenario with given id must be in DB');
        $this->assertModelData($systemGlCodeScenario, $createdSystemGlCodeScenario);
    }

    /**
     * @test read
     */
    public function test_read_system_gl_code_scenario()
    {
        $systemGlCodeScenario = factory(SystemGlCodeScenario::class)->create();

        $dbSystemGlCodeScenario = $this->systemGlCodeScenarioRepo->find($systemGlCodeScenario->id);

        $dbSystemGlCodeScenario = $dbSystemGlCodeScenario->toArray();
        $this->assertModelData($systemGlCodeScenario->toArray(), $dbSystemGlCodeScenario);
    }

    /**
     * @test update
     */
    public function test_update_system_gl_code_scenario()
    {
        $systemGlCodeScenario = factory(SystemGlCodeScenario::class)->create();
        $fakeSystemGlCodeScenario = factory(SystemGlCodeScenario::class)->make()->toArray();

        $updatedSystemGlCodeScenario = $this->systemGlCodeScenarioRepo->update($fakeSystemGlCodeScenario, $systemGlCodeScenario->id);

        $this->assertModelData($fakeSystemGlCodeScenario, $updatedSystemGlCodeScenario->toArray());
        $dbSystemGlCodeScenario = $this->systemGlCodeScenarioRepo->find($systemGlCodeScenario->id);
        $this->assertModelData($fakeSystemGlCodeScenario, $dbSystemGlCodeScenario->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_system_gl_code_scenario()
    {
        $systemGlCodeScenario = factory(SystemGlCodeScenario::class)->create();

        $resp = $this->systemGlCodeScenarioRepo->delete($systemGlCodeScenario->id);

        $this->assertTrue($resp);
        $this->assertNull(SystemGlCodeScenario::find($systemGlCodeScenario->id), 'SystemGlCodeScenario should not exist in DB');
    }
}
