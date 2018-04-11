<?php

use App\Models\PoPaymentTermTypes;
use App\Repositories\PoPaymentTermTypesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoPaymentTermTypesRepositoryTest extends TestCase
{
    use MakePoPaymentTermTypesTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoPaymentTermTypesRepository
     */
    protected $poPaymentTermTypesRepo;

    public function setUp()
    {
        parent::setUp();
        $this->poPaymentTermTypesRepo = App::make(PoPaymentTermTypesRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePoPaymentTermTypes()
    {
        $poPaymentTermTypes = $this->fakePoPaymentTermTypesData();
        $createdPoPaymentTermTypes = $this->poPaymentTermTypesRepo->create($poPaymentTermTypes);
        $createdPoPaymentTermTypes = $createdPoPaymentTermTypes->toArray();
        $this->assertArrayHasKey('id', $createdPoPaymentTermTypes);
        $this->assertNotNull($createdPoPaymentTermTypes['id'], 'Created PoPaymentTermTypes must have id specified');
        $this->assertNotNull(PoPaymentTermTypes::find($createdPoPaymentTermTypes['id']), 'PoPaymentTermTypes with given id must be in DB');
        $this->assertModelData($poPaymentTermTypes, $createdPoPaymentTermTypes);
    }

    /**
     * @test read
     */
    public function testReadPoPaymentTermTypes()
    {
        $poPaymentTermTypes = $this->makePoPaymentTermTypes();
        $dbPoPaymentTermTypes = $this->poPaymentTermTypesRepo->find($poPaymentTermTypes->id);
        $dbPoPaymentTermTypes = $dbPoPaymentTermTypes->toArray();
        $this->assertModelData($poPaymentTermTypes->toArray(), $dbPoPaymentTermTypes);
    }

    /**
     * @test update
     */
    public function testUpdatePoPaymentTermTypes()
    {
        $poPaymentTermTypes = $this->makePoPaymentTermTypes();
        $fakePoPaymentTermTypes = $this->fakePoPaymentTermTypesData();
        $updatedPoPaymentTermTypes = $this->poPaymentTermTypesRepo->update($fakePoPaymentTermTypes, $poPaymentTermTypes->id);
        $this->assertModelData($fakePoPaymentTermTypes, $updatedPoPaymentTermTypes->toArray());
        $dbPoPaymentTermTypes = $this->poPaymentTermTypesRepo->find($poPaymentTermTypes->id);
        $this->assertModelData($fakePoPaymentTermTypes, $dbPoPaymentTermTypes->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePoPaymentTermTypes()
    {
        $poPaymentTermTypes = $this->makePoPaymentTermTypes();
        $resp = $this->poPaymentTermTypesRepo->delete($poPaymentTermTypes->id);
        $this->assertTrue($resp);
        $this->assertNull(PoPaymentTermTypes::find($poPaymentTermTypes->id), 'PoPaymentTermTypes should not exist in DB');
    }
}
