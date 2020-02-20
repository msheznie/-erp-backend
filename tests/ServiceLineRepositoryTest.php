<?php namespace Tests\Repositories;

use App\Models\ServiceLine;
use App\Repositories\ServiceLineRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeServiceLineTrait;
use Tests\ApiTestTrait;

class ServiceLineRepositoryTest extends TestCase
{
    use MakeServiceLineTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ServiceLineRepository
     */
    protected $serviceLineRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->serviceLineRepo = \App::make(ServiceLineRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_service_line()
    {
        $serviceLine = $this->fakeServiceLineData();
        $createdServiceLine = $this->serviceLineRepo->create($serviceLine);
        $createdServiceLine = $createdServiceLine->toArray();
        $this->assertArrayHasKey('id', $createdServiceLine);
        $this->assertNotNull($createdServiceLine['id'], 'Created ServiceLine must have id specified');
        $this->assertNotNull(ServiceLine::find($createdServiceLine['id']), 'ServiceLine with given id must be in DB');
        $this->assertModelData($serviceLine, $createdServiceLine);
    }

    /**
     * @test read
     */
    public function test_read_service_line()
    {
        $serviceLine = $this->makeServiceLine();
        $dbServiceLine = $this->serviceLineRepo->find($serviceLine->id);
        $dbServiceLine = $dbServiceLine->toArray();
        $this->assertModelData($serviceLine->toArray(), $dbServiceLine);
    }

    /**
     * @test update
     */
    public function test_update_service_line()
    {
        $serviceLine = $this->makeServiceLine();
        $fakeServiceLine = $this->fakeServiceLineData();
        $updatedServiceLine = $this->serviceLineRepo->update($fakeServiceLine, $serviceLine->id);
        $this->assertModelData($fakeServiceLine, $updatedServiceLine->toArray());
        $dbServiceLine = $this->serviceLineRepo->find($serviceLine->id);
        $this->assertModelData($fakeServiceLine, $dbServiceLine->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_service_line()
    {
        $serviceLine = $this->makeServiceLine();
        $resp = $this->serviceLineRepo->delete($serviceLine->id);
        $this->assertTrue($resp);
        $this->assertNull(ServiceLine::find($serviceLine->id), 'ServiceLine should not exist in DB');
    }
}
