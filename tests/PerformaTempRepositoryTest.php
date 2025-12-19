<?php

use App\Models\PerformaTemp;
use App\Repositories\PerformaTempRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerformaTempRepositoryTest extends TestCase
{
    use MakePerformaTempTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PerformaTempRepository
     */
    protected $performaTempRepo;

    public function setUp()
    {
        parent::setUp();
        $this->performaTempRepo = App::make(PerformaTempRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePerformaTemp()
    {
        $performaTemp = $this->fakePerformaTempData();
        $createdPerformaTemp = $this->performaTempRepo->create($performaTemp);
        $createdPerformaTemp = $createdPerformaTemp->toArray();
        $this->assertArrayHasKey('id', $createdPerformaTemp);
        $this->assertNotNull($createdPerformaTemp['id'], 'Created PerformaTemp must have id specified');
        $this->assertNotNull(PerformaTemp::find($createdPerformaTemp['id']), 'PerformaTemp with given id must be in DB');
        $this->assertModelData($performaTemp, $createdPerformaTemp);
    }

    /**
     * @test read
     */
    public function testReadPerformaTemp()
    {
        $performaTemp = $this->makePerformaTemp();
        $dbPerformaTemp = $this->performaTempRepo->find($performaTemp->id);
        $dbPerformaTemp = $dbPerformaTemp->toArray();
        $this->assertModelData($performaTemp->toArray(), $dbPerformaTemp);
    }

    /**
     * @test update
     */
    public function testUpdatePerformaTemp()
    {
        $performaTemp = $this->makePerformaTemp();
        $fakePerformaTemp = $this->fakePerformaTempData();
        $updatedPerformaTemp = $this->performaTempRepo->update($fakePerformaTemp, $performaTemp->id);
        $this->assertModelData($fakePerformaTemp, $updatedPerformaTemp->toArray());
        $dbPerformaTemp = $this->performaTempRepo->find($performaTemp->id);
        $this->assertModelData($fakePerformaTemp, $dbPerformaTemp->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePerformaTemp()
    {
        $performaTemp = $this->makePerformaTemp();
        $resp = $this->performaTempRepo->delete($performaTemp->id);
        $this->assertTrue($resp);
        $this->assertNull(PerformaTemp::find($performaTemp->id), 'PerformaTemp should not exist in DB');
    }
}
