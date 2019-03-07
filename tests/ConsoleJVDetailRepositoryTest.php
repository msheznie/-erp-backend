<?php

use App\Models\ConsoleJVDetail;
use App\Repositories\ConsoleJVDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConsoleJVDetailRepositoryTest extends TestCase
{
    use MakeConsoleJVDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ConsoleJVDetailRepository
     */
    protected $consoleJVDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->consoleJVDetailRepo = App::make(ConsoleJVDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateConsoleJVDetail()
    {
        $consoleJVDetail = $this->fakeConsoleJVDetailData();
        $createdConsoleJVDetail = $this->consoleJVDetailRepo->create($consoleJVDetail);
        $createdConsoleJVDetail = $createdConsoleJVDetail->toArray();
        $this->assertArrayHasKey('id', $createdConsoleJVDetail);
        $this->assertNotNull($createdConsoleJVDetail['id'], 'Created ConsoleJVDetail must have id specified');
        $this->assertNotNull(ConsoleJVDetail::find($createdConsoleJVDetail['id']), 'ConsoleJVDetail with given id must be in DB');
        $this->assertModelData($consoleJVDetail, $createdConsoleJVDetail);
    }

    /**
     * @test read
     */
    public function testReadConsoleJVDetail()
    {
        $consoleJVDetail = $this->makeConsoleJVDetail();
        $dbConsoleJVDetail = $this->consoleJVDetailRepo->find($consoleJVDetail->id);
        $dbConsoleJVDetail = $dbConsoleJVDetail->toArray();
        $this->assertModelData($consoleJVDetail->toArray(), $dbConsoleJVDetail);
    }

    /**
     * @test update
     */
    public function testUpdateConsoleJVDetail()
    {
        $consoleJVDetail = $this->makeConsoleJVDetail();
        $fakeConsoleJVDetail = $this->fakeConsoleJVDetailData();
        $updatedConsoleJVDetail = $this->consoleJVDetailRepo->update($fakeConsoleJVDetail, $consoleJVDetail->id);
        $this->assertModelData($fakeConsoleJVDetail, $updatedConsoleJVDetail->toArray());
        $dbConsoleJVDetail = $this->consoleJVDetailRepo->find($consoleJVDetail->id);
        $this->assertModelData($fakeConsoleJVDetail, $dbConsoleJVDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteConsoleJVDetail()
    {
        $consoleJVDetail = $this->makeConsoleJVDetail();
        $resp = $this->consoleJVDetailRepo->delete($consoleJVDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(ConsoleJVDetail::find($consoleJVDetail->id), 'ConsoleJVDetail should not exist in DB');
    }
}
