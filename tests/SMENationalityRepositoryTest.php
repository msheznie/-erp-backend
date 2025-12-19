<?php namespace Tests\Repositories;

use App\Models\SMENationality;
use App\Repositories\SMENationalityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMENationalityRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMENationalityRepository
     */
    protected $sMENationalityRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMENationalityRepo = \App::make(SMENationalityRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_nationality()
    {
        $sMENationality = factory(SMENationality::class)->make()->toArray();

        $createdSMENationality = $this->sMENationalityRepo->create($sMENationality);

        $createdSMENationality = $createdSMENationality->toArray();
        $this->assertArrayHasKey('id', $createdSMENationality);
        $this->assertNotNull($createdSMENationality['id'], 'Created SMENationality must have id specified');
        $this->assertNotNull(SMENationality::find($createdSMENationality['id']), 'SMENationality with given id must be in DB');
        $this->assertModelData($sMENationality, $createdSMENationality);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_nationality()
    {
        $sMENationality = factory(SMENationality::class)->create();

        $dbSMENationality = $this->sMENationalityRepo->find($sMENationality->id);

        $dbSMENationality = $dbSMENationality->toArray();
        $this->assertModelData($sMENationality->toArray(), $dbSMENationality);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_nationality()
    {
        $sMENationality = factory(SMENationality::class)->create();
        $fakeSMENationality = factory(SMENationality::class)->make()->toArray();

        $updatedSMENationality = $this->sMENationalityRepo->update($fakeSMENationality, $sMENationality->id);

        $this->assertModelData($fakeSMENationality, $updatedSMENationality->toArray());
        $dbSMENationality = $this->sMENationalityRepo->find($sMENationality->id);
        $this->assertModelData($fakeSMENationality, $dbSMENationality->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_nationality()
    {
        $sMENationality = factory(SMENationality::class)->create();

        $resp = $this->sMENationalityRepo->delete($sMENationality->id);

        $this->assertTrue($resp);
        $this->assertNull(SMENationality::find($sMENationality->id), 'SMENationality should not exist in DB');
    }
}
