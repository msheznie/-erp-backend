<?php namespace Tests\Repositories;

use App\Models\SecondaryCompany;
use App\Repositories\SecondaryCompanyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSecondaryCompanyTrait;
use Tests\ApiTestTrait;

class SecondaryCompanyRepositoryTest extends TestCase
{
    use MakeSecondaryCompanyTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SecondaryCompanyRepository
     */
    protected $secondaryCompanyRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->secondaryCompanyRepo = \App::make(SecondaryCompanyRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_secondary_company()
    {
        $secondaryCompany = $this->fakeSecondaryCompanyData();
        $createdSecondaryCompany = $this->secondaryCompanyRepo->create($secondaryCompany);
        $createdSecondaryCompany = $createdSecondaryCompany->toArray();
        $this->assertArrayHasKey('id', $createdSecondaryCompany);
        $this->assertNotNull($createdSecondaryCompany['id'], 'Created SecondaryCompany must have id specified');
        $this->assertNotNull(SecondaryCompany::find($createdSecondaryCompany['id']), 'SecondaryCompany with given id must be in DB');
        $this->assertModelData($secondaryCompany, $createdSecondaryCompany);
    }

    /**
     * @test read
     */
    public function test_read_secondary_company()
    {
        $secondaryCompany = $this->makeSecondaryCompany();
        $dbSecondaryCompany = $this->secondaryCompanyRepo->find($secondaryCompany->id);
        $dbSecondaryCompany = $dbSecondaryCompany->toArray();
        $this->assertModelData($secondaryCompany->toArray(), $dbSecondaryCompany);
    }

    /**
     * @test update
     */
    public function test_update_secondary_company()
    {
        $secondaryCompany = $this->makeSecondaryCompany();
        $fakeSecondaryCompany = $this->fakeSecondaryCompanyData();
        $updatedSecondaryCompany = $this->secondaryCompanyRepo->update($fakeSecondaryCompany, $secondaryCompany->id);
        $this->assertModelData($fakeSecondaryCompany, $updatedSecondaryCompany->toArray());
        $dbSecondaryCompany = $this->secondaryCompanyRepo->find($secondaryCompany->id);
        $this->assertModelData($fakeSecondaryCompany, $dbSecondaryCompany->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_secondary_company()
    {
        $secondaryCompany = $this->makeSecondaryCompany();
        $resp = $this->secondaryCompanyRepo->delete($secondaryCompany->id);
        $this->assertTrue($resp);
        $this->assertNull(SecondaryCompany::find($secondaryCompany->id), 'SecondaryCompany should not exist in DB');
    }
}
