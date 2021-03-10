<?php namespace Tests\Repositories;

use App\Models\SMELaveGroup;
use App\Repositories\SMELaveGroupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMELaveGroupRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMELaveGroupRepository
     */
    protected $sMELaveGroupRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMELaveGroupRepo = \App::make(SMELaveGroupRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_lave_group()
    {
        $sMELaveGroup = factory(SMELaveGroup::class)->make()->toArray();

        $createdSMELaveGroup = $this->sMELaveGroupRepo->create($sMELaveGroup);

        $createdSMELaveGroup = $createdSMELaveGroup->toArray();
        $this->assertArrayHasKey('id', $createdSMELaveGroup);
        $this->assertNotNull($createdSMELaveGroup['id'], 'Created SMELaveGroup must have id specified');
        $this->assertNotNull(SMELaveGroup::find($createdSMELaveGroup['id']), 'SMELaveGroup with given id must be in DB');
        $this->assertModelData($sMELaveGroup, $createdSMELaveGroup);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_lave_group()
    {
        $sMELaveGroup = factory(SMELaveGroup::class)->create();

        $dbSMELaveGroup = $this->sMELaveGroupRepo->find($sMELaveGroup->id);

        $dbSMELaveGroup = $dbSMELaveGroup->toArray();
        $this->assertModelData($sMELaveGroup->toArray(), $dbSMELaveGroup);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_lave_group()
    {
        $sMELaveGroup = factory(SMELaveGroup::class)->create();
        $fakeSMELaveGroup = factory(SMELaveGroup::class)->make()->toArray();

        $updatedSMELaveGroup = $this->sMELaveGroupRepo->update($fakeSMELaveGroup, $sMELaveGroup->id);

        $this->assertModelData($fakeSMELaveGroup, $updatedSMELaveGroup->toArray());
        $dbSMELaveGroup = $this->sMELaveGroupRepo->find($sMELaveGroup->id);
        $this->assertModelData($fakeSMELaveGroup, $dbSMELaveGroup->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_lave_group()
    {
        $sMELaveGroup = factory(SMELaveGroup::class)->create();

        $resp = $this->sMELaveGroupRepo->delete($sMELaveGroup->id);

        $this->assertTrue($resp);
        $this->assertNull(SMELaveGroup::find($sMELaveGroup->id), 'SMELaveGroup should not exist in DB');
    }
}
