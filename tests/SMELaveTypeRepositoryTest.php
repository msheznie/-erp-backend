<?php namespace Tests\Repositories;

use App\Models\SMELaveType;
use App\Repositories\SMELaveTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMELaveTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMELaveTypeRepository
     */
    protected $sMELaveTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMELaveTypeRepo = \App::make(SMELaveTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_lave_type()
    {
        $sMELaveType = factory(SMELaveType::class)->make()->toArray();

        $createdSMELaveType = $this->sMELaveTypeRepo->create($sMELaveType);

        $createdSMELaveType = $createdSMELaveType->toArray();
        $this->assertArrayHasKey('id', $createdSMELaveType);
        $this->assertNotNull($createdSMELaveType['id'], 'Created SMELaveType must have id specified');
        $this->assertNotNull(SMELaveType::find($createdSMELaveType['id']), 'SMELaveType with given id must be in DB');
        $this->assertModelData($sMELaveType, $createdSMELaveType);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_lave_type()
    {
        $sMELaveType = factory(SMELaveType::class)->create();

        $dbSMELaveType = $this->sMELaveTypeRepo->find($sMELaveType->id);

        $dbSMELaveType = $dbSMELaveType->toArray();
        $this->assertModelData($sMELaveType->toArray(), $dbSMELaveType);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_lave_type()
    {
        $sMELaveType = factory(SMELaveType::class)->create();
        $fakeSMELaveType = factory(SMELaveType::class)->make()->toArray();

        $updatedSMELaveType = $this->sMELaveTypeRepo->update($fakeSMELaveType, $sMELaveType->id);

        $this->assertModelData($fakeSMELaveType, $updatedSMELaveType->toArray());
        $dbSMELaveType = $this->sMELaveTypeRepo->find($sMELaveType->id);
        $this->assertModelData($fakeSMELaveType, $dbSMELaveType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_lave_type()
    {
        $sMELaveType = factory(SMELaveType::class)->create();

        $resp = $this->sMELaveTypeRepo->delete($sMELaveType->id);

        $this->assertTrue($resp);
        $this->assertNull(SMELaveType::find($sMELaveType->id), 'SMELaveType should not exist in DB');
    }
}
