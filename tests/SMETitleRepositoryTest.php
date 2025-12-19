<?php namespace Tests\Repositories;

use App\Models\SMETitle;
use App\Repositories\SMETitleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMETitleRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMETitleRepository
     */
    protected $sMETitleRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMETitleRepo = \App::make(SMETitleRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_title()
    {
        $sMETitle = factory(SMETitle::class)->make()->toArray();

        $createdSMETitle = $this->sMETitleRepo->create($sMETitle);

        $createdSMETitle = $createdSMETitle->toArray();
        $this->assertArrayHasKey('id', $createdSMETitle);
        $this->assertNotNull($createdSMETitle['id'], 'Created SMETitle must have id specified');
        $this->assertNotNull(SMETitle::find($createdSMETitle['id']), 'SMETitle with given id must be in DB');
        $this->assertModelData($sMETitle, $createdSMETitle);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_title()
    {
        $sMETitle = factory(SMETitle::class)->create();

        $dbSMETitle = $this->sMETitleRepo->find($sMETitle->id);

        $dbSMETitle = $dbSMETitle->toArray();
        $this->assertModelData($sMETitle->toArray(), $dbSMETitle);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_title()
    {
        $sMETitle = factory(SMETitle::class)->create();
        $fakeSMETitle = factory(SMETitle::class)->make()->toArray();

        $updatedSMETitle = $this->sMETitleRepo->update($fakeSMETitle, $sMETitle->id);

        $this->assertModelData($fakeSMETitle, $updatedSMETitle->toArray());
        $dbSMETitle = $this->sMETitleRepo->find($sMETitle->id);
        $this->assertModelData($fakeSMETitle, $dbSMETitle->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_title()
    {
        $sMETitle = factory(SMETitle::class)->create();

        $resp = $this->sMETitleRepo->delete($sMETitle->id);

        $this->assertTrue($resp);
        $this->assertNull(SMETitle::find($sMETitle->id), 'SMETitle should not exist in DB');
    }
}
