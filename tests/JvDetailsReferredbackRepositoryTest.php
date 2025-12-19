<?php

use App\Models\JvDetailsReferredback;
use App\Repositories\JvDetailsReferredbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JvDetailsReferredbackRepositoryTest extends TestCase
{
    use MakeJvDetailsReferredbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var JvDetailsReferredbackRepository
     */
    protected $jvDetailsReferredbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->jvDetailsReferredbackRepo = App::make(JvDetailsReferredbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateJvDetailsReferredback()
    {
        $jvDetailsReferredback = $this->fakeJvDetailsReferredbackData();
        $createdJvDetailsReferredback = $this->jvDetailsReferredbackRepo->create($jvDetailsReferredback);
        $createdJvDetailsReferredback = $createdJvDetailsReferredback->toArray();
        $this->assertArrayHasKey('id', $createdJvDetailsReferredback);
        $this->assertNotNull($createdJvDetailsReferredback['id'], 'Created JvDetailsReferredback must have id specified');
        $this->assertNotNull(JvDetailsReferredback::find($createdJvDetailsReferredback['id']), 'JvDetailsReferredback with given id must be in DB');
        $this->assertModelData($jvDetailsReferredback, $createdJvDetailsReferredback);
    }

    /**
     * @test read
     */
    public function testReadJvDetailsReferredback()
    {
        $jvDetailsReferredback = $this->makeJvDetailsReferredback();
        $dbJvDetailsReferredback = $this->jvDetailsReferredbackRepo->find($jvDetailsReferredback->id);
        $dbJvDetailsReferredback = $dbJvDetailsReferredback->toArray();
        $this->assertModelData($jvDetailsReferredback->toArray(), $dbJvDetailsReferredback);
    }

    /**
     * @test update
     */
    public function testUpdateJvDetailsReferredback()
    {
        $jvDetailsReferredback = $this->makeJvDetailsReferredback();
        $fakeJvDetailsReferredback = $this->fakeJvDetailsReferredbackData();
        $updatedJvDetailsReferredback = $this->jvDetailsReferredbackRepo->update($fakeJvDetailsReferredback, $jvDetailsReferredback->id);
        $this->assertModelData($fakeJvDetailsReferredback, $updatedJvDetailsReferredback->toArray());
        $dbJvDetailsReferredback = $this->jvDetailsReferredbackRepo->find($jvDetailsReferredback->id);
        $this->assertModelData($fakeJvDetailsReferredback, $dbJvDetailsReferredback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteJvDetailsReferredback()
    {
        $jvDetailsReferredback = $this->makeJvDetailsReferredback();
        $resp = $this->jvDetailsReferredbackRepo->delete($jvDetailsReferredback->id);
        $this->assertTrue($resp);
        $this->assertNull(JvDetailsReferredback::find($jvDetailsReferredback->id), 'JvDetailsReferredback should not exist in DB');
    }
}
