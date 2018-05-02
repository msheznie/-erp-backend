<?php

use App\Models\AccessTokens;
use App\Repositories\AccessTokensRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccessTokensRepositoryTest extends TestCase
{
    use MakeAccessTokensTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccessTokensRepository
     */
    protected $accessTokensRepo;

    public function setUp()
    {
        parent::setUp();
        $this->accessTokensRepo = App::make(AccessTokensRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAccessTokens()
    {
        $accessTokens = $this->fakeAccessTokensData();
        $createdAccessTokens = $this->accessTokensRepo->create($accessTokens);
        $createdAccessTokens = $createdAccessTokens->toArray();
        $this->assertArrayHasKey('id', $createdAccessTokens);
        $this->assertNotNull($createdAccessTokens['id'], 'Created AccessTokens must have id specified');
        $this->assertNotNull(AccessTokens::find($createdAccessTokens['id']), 'AccessTokens with given id must be in DB');
        $this->assertModelData($accessTokens, $createdAccessTokens);
    }

    /**
     * @test read
     */
    public function testReadAccessTokens()
    {
        $accessTokens = $this->makeAccessTokens();
        $dbAccessTokens = $this->accessTokensRepo->find($accessTokens->id);
        $dbAccessTokens = $dbAccessTokens->toArray();
        $this->assertModelData($accessTokens->toArray(), $dbAccessTokens);
    }

    /**
     * @test update
     */
    public function testUpdateAccessTokens()
    {
        $accessTokens = $this->makeAccessTokens();
        $fakeAccessTokens = $this->fakeAccessTokensData();
        $updatedAccessTokens = $this->accessTokensRepo->update($fakeAccessTokens, $accessTokens->id);
        $this->assertModelData($fakeAccessTokens, $updatedAccessTokens->toArray());
        $dbAccessTokens = $this->accessTokensRepo->find($accessTokens->id);
        $this->assertModelData($fakeAccessTokens, $dbAccessTokens->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAccessTokens()
    {
        $accessTokens = $this->makeAccessTokens();
        $resp = $this->accessTokensRepo->delete($accessTokens->id);
        $this->assertTrue($resp);
        $this->assertNull(AccessTokens::find($accessTokens->id), 'AccessTokens should not exist in DB');
    }
}
