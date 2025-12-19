<?php namespace Tests\Repositories;

use App\Models\ERPLanguageMaster;
use App\Repositories\ERPLanguageMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ERPLanguageMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ERPLanguageMasterRepository
     */
    protected $eRPLanguageMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->eRPLanguageMasterRepo = \App::make(ERPLanguageMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_e_r_p_language_master()
    {
        $eRPLanguageMaster = factory(ERPLanguageMaster::class)->make()->toArray();

        $createdERPLanguageMaster = $this->eRPLanguageMasterRepo->create($eRPLanguageMaster);

        $createdERPLanguageMaster = $createdERPLanguageMaster->toArray();
        $this->assertArrayHasKey('id', $createdERPLanguageMaster);
        $this->assertNotNull($createdERPLanguageMaster['id'], 'Created ERPLanguageMaster must have id specified');
        $this->assertNotNull(ERPLanguageMaster::find($createdERPLanguageMaster['id']), 'ERPLanguageMaster with given id must be in DB');
        $this->assertModelData($eRPLanguageMaster, $createdERPLanguageMaster);
    }

    /**
     * @test read
     */
    public function test_read_e_r_p_language_master()
    {
        $eRPLanguageMaster = factory(ERPLanguageMaster::class)->create();

        $dbERPLanguageMaster = $this->eRPLanguageMasterRepo->find($eRPLanguageMaster->id);

        $dbERPLanguageMaster = $dbERPLanguageMaster->toArray();
        $this->assertModelData($eRPLanguageMaster->toArray(), $dbERPLanguageMaster);
    }

    /**
     * @test update
     */
    public function test_update_e_r_p_language_master()
    {
        $eRPLanguageMaster = factory(ERPLanguageMaster::class)->create();
        $fakeERPLanguageMaster = factory(ERPLanguageMaster::class)->make()->toArray();

        $updatedERPLanguageMaster = $this->eRPLanguageMasterRepo->update($fakeERPLanguageMaster, $eRPLanguageMaster->id);

        $this->assertModelData($fakeERPLanguageMaster, $updatedERPLanguageMaster->toArray());
        $dbERPLanguageMaster = $this->eRPLanguageMasterRepo->find($eRPLanguageMaster->id);
        $this->assertModelData($fakeERPLanguageMaster, $dbERPLanguageMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_e_r_p_language_master()
    {
        $eRPLanguageMaster = factory(ERPLanguageMaster::class)->create();

        $resp = $this->eRPLanguageMasterRepo->delete($eRPLanguageMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ERPLanguageMaster::find($eRPLanguageMaster->id), 'ERPLanguageMaster should not exist in DB');
    }
}
