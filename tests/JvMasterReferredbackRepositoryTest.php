<?php

use App\Models\JvMasterReferredback;
use App\Repositories\JvMasterReferredbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JvMasterReferredbackRepositoryTest extends TestCase
{
    use MakeJvMasterReferredbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var JvMasterReferredbackRepository
     */
    protected $jvMasterReferredbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->jvMasterReferredbackRepo = App::make(JvMasterReferredbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateJvMasterReferredback()
    {
        $jvMasterReferredback = $this->fakeJvMasterReferredbackData();
        $createdJvMasterReferredback = $this->jvMasterReferredbackRepo->create($jvMasterReferredback);
        $createdJvMasterReferredback = $createdJvMasterReferredback->toArray();
        $this->assertArrayHasKey('id', $createdJvMasterReferredback);
        $this->assertNotNull($createdJvMasterReferredback['id'], 'Created JvMasterReferredback must have id specified');
        $this->assertNotNull(JvMasterReferredback::find($createdJvMasterReferredback['id']), 'JvMasterReferredback with given id must be in DB');
        $this->assertModelData($jvMasterReferredback, $createdJvMasterReferredback);
    }

    /**
     * @test read
     */
    public function testReadJvMasterReferredback()
    {
        $jvMasterReferredback = $this->makeJvMasterReferredback();
        $dbJvMasterReferredback = $this->jvMasterReferredbackRepo->find($jvMasterReferredback->id);
        $dbJvMasterReferredback = $dbJvMasterReferredback->toArray();
        $this->assertModelData($jvMasterReferredback->toArray(), $dbJvMasterReferredback);
    }

    /**
     * @test update
     */
    public function testUpdateJvMasterReferredback()
    {
        $jvMasterReferredback = $this->makeJvMasterReferredback();
        $fakeJvMasterReferredback = $this->fakeJvMasterReferredbackData();
        $updatedJvMasterReferredback = $this->jvMasterReferredbackRepo->update($fakeJvMasterReferredback, $jvMasterReferredback->id);
        $this->assertModelData($fakeJvMasterReferredback, $updatedJvMasterReferredback->toArray());
        $dbJvMasterReferredback = $this->jvMasterReferredbackRepo->find($jvMasterReferredback->id);
        $this->assertModelData($fakeJvMasterReferredback, $dbJvMasterReferredback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteJvMasterReferredback()
    {
        $jvMasterReferredback = $this->makeJvMasterReferredback();
        $resp = $this->jvMasterReferredbackRepo->delete($jvMasterReferredback->id);
        $this->assertTrue($resp);
        $this->assertNull(JvMasterReferredback::find($jvMasterReferredback->id), 'JvMasterReferredback should not exist in DB');
    }
}
