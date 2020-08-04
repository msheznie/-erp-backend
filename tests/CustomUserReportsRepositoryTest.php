<?php namespace Tests\Repositories;

use App\Models\CustomUserReports;
use App\Repositories\CustomUserReportsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomUserReportsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomUserReportsRepository
     */
    protected $customUserReportsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customUserReportsRepo = \App::make(CustomUserReportsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_custom_user_reports()
    {
        $customUserReports = factory(CustomUserReports::class)->make()->toArray();

        $createdCustomUserReports = $this->customUserReportsRepo->create($customUserReports);

        $createdCustomUserReports = $createdCustomUserReports->toArray();
        $this->assertArrayHasKey('id', $createdCustomUserReports);
        $this->assertNotNull($createdCustomUserReports['id'], 'Created CustomUserReports must have id specified');
        $this->assertNotNull(CustomUserReports::find($createdCustomUserReports['id']), 'CustomUserReports with given id must be in DB');
        $this->assertModelData($customUserReports, $createdCustomUserReports);
    }

    /**
     * @test read
     */
    public function test_read_custom_user_reports()
    {
        $customUserReports = factory(CustomUserReports::class)->create();

        $dbCustomUserReports = $this->customUserReportsRepo->find($customUserReports->id);

        $dbCustomUserReports = $dbCustomUserReports->toArray();
        $this->assertModelData($customUserReports->toArray(), $dbCustomUserReports);
    }

    /**
     * @test update
     */
    public function test_update_custom_user_reports()
    {
        $customUserReports = factory(CustomUserReports::class)->create();
        $fakeCustomUserReports = factory(CustomUserReports::class)->make()->toArray();

        $updatedCustomUserReports = $this->customUserReportsRepo->update($fakeCustomUserReports, $customUserReports->id);

        $this->assertModelData($fakeCustomUserReports, $updatedCustomUserReports->toArray());
        $dbCustomUserReports = $this->customUserReportsRepo->find($customUserReports->id);
        $this->assertModelData($fakeCustomUserReports, $dbCustomUserReports->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_custom_user_reports()
    {
        $customUserReports = factory(CustomUserReports::class)->create();

        $resp = $this->customUserReportsRepo->delete($customUserReports->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomUserReports::find($customUserReports->id), 'CustomUserReports should not exist in DB');
    }
}
