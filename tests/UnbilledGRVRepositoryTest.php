<?php

use App\Models\UnbilledGRV;
use App\Repositories\UnbilledGRVRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnbilledGRVRepositoryTest extends TestCase
{
    use MakeUnbilledGRVTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UnbilledGRVRepository
     */
    protected $unbilledGRVRepo;

    public function setUp()
    {
        parent::setUp();
        $this->unbilledGRVRepo = App::make(UnbilledGRVRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUnbilledGRV()
    {
        $unbilledGRV = $this->fakeUnbilledGRVData();
        $createdUnbilledGRV = $this->unbilledGRVRepo->create($unbilledGRV);
        $createdUnbilledGRV = $createdUnbilledGRV->toArray();
        $this->assertArrayHasKey('id', $createdUnbilledGRV);
        $this->assertNotNull($createdUnbilledGRV['id'], 'Created UnbilledGRV must have id specified');
        $this->assertNotNull(UnbilledGRV::find($createdUnbilledGRV['id']), 'UnbilledGRV with given id must be in DB');
        $this->assertModelData($unbilledGRV, $createdUnbilledGRV);
    }

    /**
     * @test read
     */
    public function testReadUnbilledGRV()
    {
        $unbilledGRV = $this->makeUnbilledGRV();
        $dbUnbilledGRV = $this->unbilledGRVRepo->find($unbilledGRV->id);
        $dbUnbilledGRV = $dbUnbilledGRV->toArray();
        $this->assertModelData($unbilledGRV->toArray(), $dbUnbilledGRV);
    }

    /**
     * @test update
     */
    public function testUpdateUnbilledGRV()
    {
        $unbilledGRV = $this->makeUnbilledGRV();
        $fakeUnbilledGRV = $this->fakeUnbilledGRVData();
        $updatedUnbilledGRV = $this->unbilledGRVRepo->update($fakeUnbilledGRV, $unbilledGRV->id);
        $this->assertModelData($fakeUnbilledGRV, $updatedUnbilledGRV->toArray());
        $dbUnbilledGRV = $this->unbilledGRVRepo->find($unbilledGRV->id);
        $this->assertModelData($fakeUnbilledGRV, $dbUnbilledGRV->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUnbilledGRV()
    {
        $unbilledGRV = $this->makeUnbilledGRV();
        $resp = $this->unbilledGRVRepo->delete($unbilledGRV->id);
        $this->assertTrue($resp);
        $this->assertNull(UnbilledGRV::find($unbilledGRV->id), 'UnbilledGRV should not exist in DB');
    }
}
