<?php

use App\Models\JvMaster;
use App\Repositories\JvMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JvMasterRepositoryTest extends TestCase
{
    use MakeJvMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var JvMasterRepository
     */
    protected $jvMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->jvMasterRepo = App::make(JvMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateJvMaster()
    {
        $jvMaster = $this->fakeJvMasterData();
        $createdJvMaster = $this->jvMasterRepo->create($jvMaster);
        $createdJvMaster = $createdJvMaster->toArray();
        $this->assertArrayHasKey('id', $createdJvMaster);
        $this->assertNotNull($createdJvMaster['id'], 'Created JvMaster must have id specified');
        $this->assertNotNull(JvMaster::find($createdJvMaster['id']), 'JvMaster with given id must be in DB');
        $this->assertModelData($jvMaster, $createdJvMaster);
    }

    /**
     * @test read
     */
    public function testReadJvMaster()
    {
        $jvMaster = $this->makeJvMaster();
        $dbJvMaster = $this->jvMasterRepo->find($jvMaster->id);
        $dbJvMaster = $dbJvMaster->toArray();
        $this->assertModelData($jvMaster->toArray(), $dbJvMaster);
    }

    /**
     * @test update
     */
    public function testUpdateJvMaster()
    {
        $jvMaster = $this->makeJvMaster();
        $fakeJvMaster = $this->fakeJvMasterData();
        $updatedJvMaster = $this->jvMasterRepo->update($fakeJvMaster, $jvMaster->id);
        $this->assertModelData($fakeJvMaster, $updatedJvMaster->toArray());
        $dbJvMaster = $this->jvMasterRepo->find($jvMaster->id);
        $this->assertModelData($fakeJvMaster, $dbJvMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteJvMaster()
    {
        $jvMaster = $this->makeJvMaster();
        $resp = $this->jvMasterRepo->delete($jvMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(JvMaster::find($jvMaster->id), 'JvMaster should not exist in DB');
    }
}
