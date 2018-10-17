<?php

use App\Models\TemplatesMaster;
use App\Repositories\TemplatesMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplatesMasterRepositoryTest extends TestCase
{
    use MakeTemplatesMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TemplatesMasterRepository
     */
    protected $templatesMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->templatesMasterRepo = App::make(TemplatesMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTemplatesMaster()
    {
        $templatesMaster = $this->fakeTemplatesMasterData();
        $createdTemplatesMaster = $this->templatesMasterRepo->create($templatesMaster);
        $createdTemplatesMaster = $createdTemplatesMaster->toArray();
        $this->assertArrayHasKey('id', $createdTemplatesMaster);
        $this->assertNotNull($createdTemplatesMaster['id'], 'Created TemplatesMaster must have id specified');
        $this->assertNotNull(TemplatesMaster::find($createdTemplatesMaster['id']), 'TemplatesMaster with given id must be in DB');
        $this->assertModelData($templatesMaster, $createdTemplatesMaster);
    }

    /**
     * @test read
     */
    public function testReadTemplatesMaster()
    {
        $templatesMaster = $this->makeTemplatesMaster();
        $dbTemplatesMaster = $this->templatesMasterRepo->find($templatesMaster->id);
        $dbTemplatesMaster = $dbTemplatesMaster->toArray();
        $this->assertModelData($templatesMaster->toArray(), $dbTemplatesMaster);
    }

    /**
     * @test update
     */
    public function testUpdateTemplatesMaster()
    {
        $templatesMaster = $this->makeTemplatesMaster();
        $fakeTemplatesMaster = $this->fakeTemplatesMasterData();
        $updatedTemplatesMaster = $this->templatesMasterRepo->update($fakeTemplatesMaster, $templatesMaster->id);
        $this->assertModelData($fakeTemplatesMaster, $updatedTemplatesMaster->toArray());
        $dbTemplatesMaster = $this->templatesMasterRepo->find($templatesMaster->id);
        $this->assertModelData($fakeTemplatesMaster, $dbTemplatesMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTemplatesMaster()
    {
        $templatesMaster = $this->makeTemplatesMaster();
        $resp = $this->templatesMasterRepo->delete($templatesMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(TemplatesMaster::find($templatesMaster->id), 'TemplatesMaster should not exist in DB');
    }
}
