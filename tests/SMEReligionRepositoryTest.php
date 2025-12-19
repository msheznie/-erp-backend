<?php namespace Tests\Repositories;

use App\Models\SMEReligion;
use App\Repositories\SMEReligionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMEReligionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMEReligionRepository
     */
    protected $sMEReligionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMEReligionRepo = \App::make(SMEReligionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_religion()
    {
        $sMEReligion = factory(SMEReligion::class)->make()->toArray();

        $createdSMEReligion = $this->sMEReligionRepo->create($sMEReligion);

        $createdSMEReligion = $createdSMEReligion->toArray();
        $this->assertArrayHasKey('id', $createdSMEReligion);
        $this->assertNotNull($createdSMEReligion['id'], 'Created SMEReligion must have id specified');
        $this->assertNotNull(SMEReligion::find($createdSMEReligion['id']), 'SMEReligion with given id must be in DB');
        $this->assertModelData($sMEReligion, $createdSMEReligion);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_religion()
    {
        $sMEReligion = factory(SMEReligion::class)->create();

        $dbSMEReligion = $this->sMEReligionRepo->find($sMEReligion->id);

        $dbSMEReligion = $dbSMEReligion->toArray();
        $this->assertModelData($sMEReligion->toArray(), $dbSMEReligion);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_religion()
    {
        $sMEReligion = factory(SMEReligion::class)->create();
        $fakeSMEReligion = factory(SMEReligion::class)->make()->toArray();

        $updatedSMEReligion = $this->sMEReligionRepo->update($fakeSMEReligion, $sMEReligion->id);

        $this->assertModelData($fakeSMEReligion, $updatedSMEReligion->toArray());
        $dbSMEReligion = $this->sMEReligionRepo->find($sMEReligion->id);
        $this->assertModelData($fakeSMEReligion, $dbSMEReligion->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_religion()
    {
        $sMEReligion = factory(SMEReligion::class)->create();

        $resp = $this->sMEReligionRepo->delete($sMEReligion->id);

        $this->assertTrue($resp);
        $this->assertNull(SMEReligion::find($sMEReligion->id), 'SMEReligion should not exist in DB');
    }
}
