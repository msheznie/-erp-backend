<?php

use App\Models\GRVTypes;
use App\Repositories\GRVTypesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GRVTypesRepositoryTest extends TestCase
{
    use MakeGRVTypesTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GRVTypesRepository
     */
    protected $gRVTypesRepo;

    public function setUp()
    {
        parent::setUp();
        $this->gRVTypesRepo = App::make(GRVTypesRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGRVTypes()
    {
        $gRVTypes = $this->fakeGRVTypesData();
        $createdGRVTypes = $this->gRVTypesRepo->create($gRVTypes);
        $createdGRVTypes = $createdGRVTypes->toArray();
        $this->assertArrayHasKey('id', $createdGRVTypes);
        $this->assertNotNull($createdGRVTypes['id'], 'Created GRVTypes must have id specified');
        $this->assertNotNull(GRVTypes::find($createdGRVTypes['id']), 'GRVTypes with given id must be in DB');
        $this->assertModelData($gRVTypes, $createdGRVTypes);
    }

    /**
     * @test read
     */
    public function testReadGRVTypes()
    {
        $gRVTypes = $this->makeGRVTypes();
        $dbGRVTypes = $this->gRVTypesRepo->find($gRVTypes->id);
        $dbGRVTypes = $dbGRVTypes->toArray();
        $this->assertModelData($gRVTypes->toArray(), $dbGRVTypes);
    }

    /**
     * @test update
     */
    public function testUpdateGRVTypes()
    {
        $gRVTypes = $this->makeGRVTypes();
        $fakeGRVTypes = $this->fakeGRVTypesData();
        $updatedGRVTypes = $this->gRVTypesRepo->update($fakeGRVTypes, $gRVTypes->id);
        $this->assertModelData($fakeGRVTypes, $updatedGRVTypes->toArray());
        $dbGRVTypes = $this->gRVTypesRepo->find($gRVTypes->id);
        $this->assertModelData($fakeGRVTypes, $dbGRVTypes->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGRVTypes()
    {
        $gRVTypes = $this->makeGRVTypes();
        $resp = $this->gRVTypesRepo->delete($gRVTypes->id);
        $this->assertTrue($resp);
        $this->assertNull(GRVTypes::find($gRVTypes->id), 'GRVTypes should not exist in DB');
    }
}
