<?php namespace Tests\Repositories;

use App\Models\FcmToken;
use App\Repositories\FcmTokenRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FcmTokenRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FcmTokenRepository
     */
    protected $fcmTokenRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->fcmTokenRepo = \App::make(FcmTokenRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_fcm_token()
    {
        $fcmToken = factory(FcmToken::class)->make()->toArray();

        $createdFcmToken = $this->fcmTokenRepo->create($fcmToken);

        $createdFcmToken = $createdFcmToken->toArray();
        $this->assertArrayHasKey('id', $createdFcmToken);
        $this->assertNotNull($createdFcmToken['id'], 'Created FcmToken must have id specified');
        $this->assertNotNull(FcmToken::find($createdFcmToken['id']), 'FcmToken with given id must be in DB');
        $this->assertModelData($fcmToken, $createdFcmToken);
    }

    /**
     * @test read
     */
    public function test_read_fcm_token()
    {
        $fcmToken = factory(FcmToken::class)->create();

        $dbFcmToken = $this->fcmTokenRepo->find($fcmToken->id);

        $dbFcmToken = $dbFcmToken->toArray();
        $this->assertModelData($fcmToken->toArray(), $dbFcmToken);
    }

    /**
     * @test update
     */
    public function test_update_fcm_token()
    {
        $fcmToken = factory(FcmToken::class)->create();
        $fakeFcmToken = factory(FcmToken::class)->make()->toArray();

        $updatedFcmToken = $this->fcmTokenRepo->update($fakeFcmToken, $fcmToken->id);

        $this->assertModelData($fakeFcmToken, $updatedFcmToken->toArray());
        $dbFcmToken = $this->fcmTokenRepo->find($fcmToken->id);
        $this->assertModelData($fakeFcmToken, $dbFcmToken->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_fcm_token()
    {
        $fcmToken = factory(FcmToken::class)->create();

        $resp = $this->fcmTokenRepo->delete($fcmToken->id);

        $this->assertTrue($resp);
        $this->assertNull(FcmToken::find($fcmToken->id), 'FcmToken should not exist in DB');
    }
}
