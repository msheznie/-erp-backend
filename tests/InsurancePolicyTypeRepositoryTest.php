<?php

use App\Models\InsurancePolicyType;
use App\Repositories\InsurancePolicyTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InsurancePolicyTypeRepositoryTest extends TestCase
{
    use MakeInsurancePolicyTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var InsurancePolicyTypeRepository
     */
    protected $insurancePolicyTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->insurancePolicyTypeRepo = App::make(InsurancePolicyTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateInsurancePolicyType()
    {
        $insurancePolicyType = $this->fakeInsurancePolicyTypeData();
        $createdInsurancePolicyType = $this->insurancePolicyTypeRepo->create($insurancePolicyType);
        $createdInsurancePolicyType = $createdInsurancePolicyType->toArray();
        $this->assertArrayHasKey('id', $createdInsurancePolicyType);
        $this->assertNotNull($createdInsurancePolicyType['id'], 'Created InsurancePolicyType must have id specified');
        $this->assertNotNull(InsurancePolicyType::find($createdInsurancePolicyType['id']), 'InsurancePolicyType with given id must be in DB');
        $this->assertModelData($insurancePolicyType, $createdInsurancePolicyType);
    }

    /**
     * @test read
     */
    public function testReadInsurancePolicyType()
    {
        $insurancePolicyType = $this->makeInsurancePolicyType();
        $dbInsurancePolicyType = $this->insurancePolicyTypeRepo->find($insurancePolicyType->id);
        $dbInsurancePolicyType = $dbInsurancePolicyType->toArray();
        $this->assertModelData($insurancePolicyType->toArray(), $dbInsurancePolicyType);
    }

    /**
     * @test update
     */
    public function testUpdateInsurancePolicyType()
    {
        $insurancePolicyType = $this->makeInsurancePolicyType();
        $fakeInsurancePolicyType = $this->fakeInsurancePolicyTypeData();
        $updatedInsurancePolicyType = $this->insurancePolicyTypeRepo->update($fakeInsurancePolicyType, $insurancePolicyType->id);
        $this->assertModelData($fakeInsurancePolicyType, $updatedInsurancePolicyType->toArray());
        $dbInsurancePolicyType = $this->insurancePolicyTypeRepo->find($insurancePolicyType->id);
        $this->assertModelData($fakeInsurancePolicyType, $dbInsurancePolicyType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteInsurancePolicyType()
    {
        $insurancePolicyType = $this->makeInsurancePolicyType();
        $resp = $this->insurancePolicyTypeRepo->delete($insurancePolicyType->id);
        $this->assertTrue($resp);
        $this->assertNull(InsurancePolicyType::find($insurancePolicyType->id), 'InsurancePolicyType should not exist in DB');
    }
}
