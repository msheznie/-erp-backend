<?php namespace Tests\Repositories;

use App\Models\SMECountry;
use App\Repositories\SMECountryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMECountryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMECountryRepository
     */
    protected $sMECountryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMECountryRepo = \App::make(SMECountryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_country()
    {
        $sMECountry = factory(SMECountry::class)->make()->toArray();

        $createdSMECountry = $this->sMECountryRepo->create($sMECountry);

        $createdSMECountry = $createdSMECountry->toArray();
        $this->assertArrayHasKey('id', $createdSMECountry);
        $this->assertNotNull($createdSMECountry['id'], 'Created SMECountry must have id specified');
        $this->assertNotNull(SMECountry::find($createdSMECountry['id']), 'SMECountry with given id must be in DB');
        $this->assertModelData($sMECountry, $createdSMECountry);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_country()
    {
        $sMECountry = factory(SMECountry::class)->create();

        $dbSMECountry = $this->sMECountryRepo->find($sMECountry->id);

        $dbSMECountry = $dbSMECountry->toArray();
        $this->assertModelData($sMECountry->toArray(), $dbSMECountry);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_country()
    {
        $sMECountry = factory(SMECountry::class)->create();
        $fakeSMECountry = factory(SMECountry::class)->make()->toArray();

        $updatedSMECountry = $this->sMECountryRepo->update($fakeSMECountry, $sMECountry->id);

        $this->assertModelData($fakeSMECountry, $updatedSMECountry->toArray());
        $dbSMECountry = $this->sMECountryRepo->find($sMECountry->id);
        $this->assertModelData($fakeSMECountry, $dbSMECountry->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_country()
    {
        $sMECountry = factory(SMECountry::class)->create();

        $resp = $this->sMECountryRepo->delete($sMECountry->id);

        $this->assertTrue($resp);
        $this->assertNull(SMECountry::find($sMECountry->id), 'SMECountry should not exist in DB');
    }
}
