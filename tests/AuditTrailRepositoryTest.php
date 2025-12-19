<?php

use App\Models\AuditTrail;
use App\Repositories\AuditTrailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuditTrailRepositoryTest extends TestCase
{
    use MakeAuditTrailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AuditTrailRepository
     */
    protected $auditTrailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->auditTrailRepo = App::make(AuditTrailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAuditTrail()
    {
        $auditTrail = $this->fakeAuditTrailData();
        $createdAuditTrail = $this->auditTrailRepo->create($auditTrail);
        $createdAuditTrail = $createdAuditTrail->toArray();
        $this->assertArrayHasKey('id', $createdAuditTrail);
        $this->assertNotNull($createdAuditTrail['id'], 'Created AuditTrail must have id specified');
        $this->assertNotNull(AuditTrail::find($createdAuditTrail['id']), 'AuditTrail with given id must be in DB');
        $this->assertModelData($auditTrail, $createdAuditTrail);
    }

    /**
     * @test read
     */
    public function testReadAuditTrail()
    {
        $auditTrail = $this->makeAuditTrail();
        $dbAuditTrail = $this->auditTrailRepo->find($auditTrail->id);
        $dbAuditTrail = $dbAuditTrail->toArray();
        $this->assertModelData($auditTrail->toArray(), $dbAuditTrail);
    }

    /**
     * @test update
     */
    public function testUpdateAuditTrail()
    {
        $auditTrail = $this->makeAuditTrail();
        $fakeAuditTrail = $this->fakeAuditTrailData();
        $updatedAuditTrail = $this->auditTrailRepo->update($fakeAuditTrail, $auditTrail->id);
        $this->assertModelData($fakeAuditTrail, $updatedAuditTrail->toArray());
        $dbAuditTrail = $this->auditTrailRepo->find($auditTrail->id);
        $this->assertModelData($fakeAuditTrail, $dbAuditTrail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAuditTrail()
    {
        $auditTrail = $this->makeAuditTrail();
        $resp = $this->auditTrailRepo->delete($auditTrail->id);
        $this->assertTrue($resp);
        $this->assertNull(AuditTrail::find($auditTrail->id), 'AuditTrail should not exist in DB');
    }
}
