<?php namespace Tests\Repositories;

use App\Models\GrvTypeLanguage;
use App\Repositories\GrvTypeLanguageRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class GrvTypeLanguageRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var GrvTypeLanguageRepository
     */
    protected $grvTypeLanguageRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->grvTypeLanguageRepo = \App::make(GrvTypeLanguageRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_grv_type_language()
    {
        $grvTypeLanguage = factory(GrvTypeLanguage::class)->make()->toArray();

        $createdGrvTypeLanguage = $this->grvTypeLanguageRepo->create($grvTypeLanguage);

        $createdGrvTypeLanguage = $createdGrvTypeLanguage->toArray();
        $this->assertArrayHasKey('id', $createdGrvTypeLanguage);
        $this->assertNotNull($createdGrvTypeLanguage['id'], 'Created GrvTypeLanguage must have id specified');
        $this->assertNotNull(GrvTypeLanguage::find($createdGrvTypeLanguage['id']), 'GrvTypeLanguage with given id must be in DB');
        $this->assertModelData($grvTypeLanguage, $createdGrvTypeLanguage);
    }

    /**
     * @test read
     */
    public function test_read_grv_type_language()
    {
        $grvTypeLanguage = factory(GrvTypeLanguage::class)->create();

        $dbGrvTypeLanguage = $this->grvTypeLanguageRepo->find($grvTypeLanguage->id);

        $dbGrvTypeLanguage = $dbGrvTypeLanguage->toArray();
        $this->assertModelData($grvTypeLanguage->toArray(), $dbGrvTypeLanguage);
    }

    /**
     * @test update
     */
    public function test_update_grv_type_language()
    {
        $grvTypeLanguage = factory(GrvTypeLanguage::class)->create();
        $fakeGrvTypeLanguage = factory(GrvTypeLanguage::class)->make()->toArray();

        $updatedGrvTypeLanguage = $this->grvTypeLanguageRepo->update($fakeGrvTypeLanguage, $grvTypeLanguage->id);

        $this->assertModelData($fakeGrvTypeLanguage, $updatedGrvTypeLanguage->toArray());
        $dbGrvTypeLanguage = $this->grvTypeLanguageRepo->find($grvTypeLanguage->id);
        $this->assertModelData($fakeGrvTypeLanguage, $dbGrvTypeLanguage->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_grv_type_language()
    {
        $grvTypeLanguage = factory(GrvTypeLanguage::class)->create();

        $resp = $this->grvTypeLanguageRepo->delete($grvTypeLanguage->id);

        $this->assertTrue($resp);
        $this->assertNull(GrvTypeLanguage::find($grvTypeLanguage->id), 'GrvTypeLanguage should not exist in DB');
    }
}
