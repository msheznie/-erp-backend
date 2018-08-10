<?php

use App\Models\FieldMaster;
use App\Repositories\FieldMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FieldMasterRepositoryTest extends TestCase
{
    use MakeFieldMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FieldMasterRepository
     */
    protected $fieldMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fieldMasterRepo = App::make(FieldMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFieldMaster()
    {
        $fieldMaster = $this->fakeFieldMasterData();
        $createdFieldMaster = $this->fieldMasterRepo->create($fieldMaster);
        $createdFieldMaster = $createdFieldMaster->toArray();
        $this->assertArrayHasKey('id', $createdFieldMaster);
        $this->assertNotNull($createdFieldMaster['id'], 'Created FieldMaster must have id specified');
        $this->assertNotNull(FieldMaster::find($createdFieldMaster['id']), 'FieldMaster with given id must be in DB');
        $this->assertModelData($fieldMaster, $createdFieldMaster);
    }

    /**
     * @test read
     */
    public function testReadFieldMaster()
    {
        $fieldMaster = $this->makeFieldMaster();
        $dbFieldMaster = $this->fieldMasterRepo->find($fieldMaster->id);
        $dbFieldMaster = $dbFieldMaster->toArray();
        $this->assertModelData($fieldMaster->toArray(), $dbFieldMaster);
    }

    /**
     * @test update
     */
    public function testUpdateFieldMaster()
    {
        $fieldMaster = $this->makeFieldMaster();
        $fakeFieldMaster = $this->fakeFieldMasterData();
        $updatedFieldMaster = $this->fieldMasterRepo->update($fakeFieldMaster, $fieldMaster->id);
        $this->assertModelData($fakeFieldMaster, $updatedFieldMaster->toArray());
        $dbFieldMaster = $this->fieldMasterRepo->find($fieldMaster->id);
        $this->assertModelData($fakeFieldMaster, $dbFieldMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFieldMaster()
    {
        $fieldMaster = $this->makeFieldMaster();
        $resp = $this->fieldMasterRepo->delete($fieldMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(FieldMaster::find($fieldMaster->id), 'FieldMaster should not exist in DB');
    }
}
