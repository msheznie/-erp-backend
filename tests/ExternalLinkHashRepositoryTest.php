<?php namespace Tests\Repositories;

use App\Models\ExternalLinkHash;
use App\Repositories\ExternalLinkHashRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExternalLinkHashRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExternalLinkHashRepository
     */
    protected $externalLinkHashRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->externalLinkHashRepo = \App::make(ExternalLinkHashRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_external_link_hash()
    {
        $externalLinkHash = factory(ExternalLinkHash::class)->make()->toArray();

        $createdExternalLinkHash = $this->externalLinkHashRepo->create($externalLinkHash);

        $createdExternalLinkHash = $createdExternalLinkHash->toArray();
        $this->assertArrayHasKey('id', $createdExternalLinkHash);
        $this->assertNotNull($createdExternalLinkHash['id'], 'Created ExternalLinkHash must have id specified');
        $this->assertNotNull(ExternalLinkHash::find($createdExternalLinkHash['id']), 'ExternalLinkHash with given id must be in DB');
        $this->assertModelData($externalLinkHash, $createdExternalLinkHash);
    }

    /**
     * @test read
     */
    public function test_read_external_link_hash()
    {
        $externalLinkHash = factory(ExternalLinkHash::class)->create();

        $dbExternalLinkHash = $this->externalLinkHashRepo->find($externalLinkHash->id);

        $dbExternalLinkHash = $dbExternalLinkHash->toArray();
        $this->assertModelData($externalLinkHash->toArray(), $dbExternalLinkHash);
    }

    /**
     * @test update
     */
    public function test_update_external_link_hash()
    {
        $externalLinkHash = factory(ExternalLinkHash::class)->create();
        $fakeExternalLinkHash = factory(ExternalLinkHash::class)->make()->toArray();

        $updatedExternalLinkHash = $this->externalLinkHashRepo->update($fakeExternalLinkHash, $externalLinkHash->id);

        $this->assertModelData($fakeExternalLinkHash, $updatedExternalLinkHash->toArray());
        $dbExternalLinkHash = $this->externalLinkHashRepo->find($externalLinkHash->id);
        $this->assertModelData($fakeExternalLinkHash, $dbExternalLinkHash->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_external_link_hash()
    {
        $externalLinkHash = factory(ExternalLinkHash::class)->create();

        $resp = $this->externalLinkHashRepo->delete($externalLinkHash->id);

        $this->assertTrue($resp);
        $this->assertNull(ExternalLinkHash::find($externalLinkHash->id), 'ExternalLinkHash should not exist in DB');
    }
}
