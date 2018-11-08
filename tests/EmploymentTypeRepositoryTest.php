<?php

use App\Models\EmploymentType;
use App\Repositories\EmploymentTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmploymentTypeRepositoryTest extends TestCase
{
    use MakeEmploymentTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmploymentTypeRepository
     */
    protected $employmentTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->employmentTypeRepo = App::make(EmploymentTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateEmploymentType()
    {
        $employmentType = $this->fakeEmploymentTypeData();
        $createdEmploymentType = $this->employmentTypeRepo->create($employmentType);
        $createdEmploymentType = $createdEmploymentType->toArray();
        $this->assertArrayHasKey('id', $createdEmploymentType);
        $this->assertNotNull($createdEmploymentType['id'], 'Created EmploymentType must have id specified');
        $this->assertNotNull(EmploymentType::find($createdEmploymentType['id']), 'EmploymentType with given id must be in DB');
        $this->assertModelData($employmentType, $createdEmploymentType);
    }

    /**
     * @test read
     */
    public function testReadEmploymentType()
    {
        $employmentType = $this->makeEmploymentType();
        $dbEmploymentType = $this->employmentTypeRepo->find($employmentType->id);
        $dbEmploymentType = $dbEmploymentType->toArray();
        $this->assertModelData($employmentType->toArray(), $dbEmploymentType);
    }

    /**
     * @test update
     */
    public function testUpdateEmploymentType()
    {
        $employmentType = $this->makeEmploymentType();
        $fakeEmploymentType = $this->fakeEmploymentTypeData();
        $updatedEmploymentType = $this->employmentTypeRepo->update($fakeEmploymentType, $employmentType->id);
        $this->assertModelData($fakeEmploymentType, $updatedEmploymentType->toArray());
        $dbEmploymentType = $this->employmentTypeRepo->find($employmentType->id);
        $this->assertModelData($fakeEmploymentType, $dbEmploymentType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteEmploymentType()
    {
        $employmentType = $this->makeEmploymentType();
        $resp = $this->employmentTypeRepo->delete($employmentType->id);
        $this->assertTrue($resp);
        $this->assertNull(EmploymentType::find($employmentType->id), 'EmploymentType should not exist in DB');
    }
}
