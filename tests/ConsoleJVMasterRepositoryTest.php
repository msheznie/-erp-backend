<?php

use App\Models\ConsoleJVMaster;
use App\Repositories\ConsoleJVMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConsoleJVMasterRepositoryTest extends TestCase
{
    use MakeConsoleJVMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ConsoleJVMasterRepository
     */
    protected $consoleJVMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->consoleJVMasterRepo = App::make(ConsoleJVMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateConsoleJVMaster()
    {
        $consoleJVMaster = $this->fakeConsoleJVMasterData();
        $createdConsoleJVMaster = $this->consoleJVMasterRepo->create($consoleJVMaster);
        $createdConsoleJVMaster = $createdConsoleJVMaster->toArray();
        $this->assertArrayHasKey('id', $createdConsoleJVMaster);
        $this->assertNotNull($createdConsoleJVMaster['id'], 'Created ConsoleJVMaster must have id specified');
        $this->assertNotNull(ConsoleJVMaster::find($createdConsoleJVMaster['id']), 'ConsoleJVMaster with given id must be in DB');
        $this->assertModelData($consoleJVMaster, $createdConsoleJVMaster);
    }

    /**
     * @test read
     */
    public function testReadConsoleJVMaster()
    {
        $consoleJVMaster = $this->makeConsoleJVMaster();
        $dbConsoleJVMaster = $this->consoleJVMasterRepo->find($consoleJVMaster->id);
        $dbConsoleJVMaster = $dbConsoleJVMaster->toArray();
        $this->assertModelData($consoleJVMaster->toArray(), $dbConsoleJVMaster);
    }

    /**
     * @test update
     */
    public function testUpdateConsoleJVMaster()
    {
        $consoleJVMaster = $this->makeConsoleJVMaster();
        $fakeConsoleJVMaster = $this->fakeConsoleJVMasterData();
        $updatedConsoleJVMaster = $this->consoleJVMasterRepo->update($fakeConsoleJVMaster, $consoleJVMaster->id);
        $this->assertModelData($fakeConsoleJVMaster, $updatedConsoleJVMaster->toArray());
        $dbConsoleJVMaster = $this->consoleJVMasterRepo->find($consoleJVMaster->id);
        $this->assertModelData($fakeConsoleJVMaster, $dbConsoleJVMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteConsoleJVMaster()
    {
        $consoleJVMaster = $this->makeConsoleJVMaster();
        $resp = $this->consoleJVMasterRepo->delete($consoleJVMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(ConsoleJVMaster::find($consoleJVMaster->id), 'ConsoleJVMaster should not exist in DB');
    }
}
