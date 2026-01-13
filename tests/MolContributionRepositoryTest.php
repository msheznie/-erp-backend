<?php namespace Tests\Repositories;

use App\Models\MolContribution;
use App\Repositories\MolContributionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MolContributionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MolContributionRepository
     */
    protected $molContributionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->molContributionRepo = \App::make(MolContributionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mol_contribution()
    {
        $molContribution = factory(MolContribution::class)->make()->toArray();

        $createdMolContribution = $this->molContributionRepo->create($molContribution);

        $createdMolContribution = $createdMolContribution->toArray();
        $this->assertArrayHasKey('id', $createdMolContribution);
        $this->assertNotNull($createdMolContribution['id'], 'Created MolContribution must have id specified');
        $this->assertNotNull(MolContribution::find($createdMolContribution['id']), 'MolContribution with given id must be in DB');
        $this->assertModelData($molContribution, $createdMolContribution);
    }

    /**
     * @test read
     */
    public function test_read_mol_contribution()
    {
        $molContribution = factory(MolContribution::class)->create();

        $dbMolContribution = $this->molContributionRepo->find($molContribution->id);

        $dbMolContribution = $dbMolContribution->toArray();
        $this->assertModelData($molContribution->toArray(), $dbMolContribution);
    }

    /**
     * @test update
     */
    public function test_update_mol_contribution()
    {
        $molContribution = factory(MolContribution::class)->create();
        $fakeMolContribution = factory(MolContribution::class)->make()->toArray();

        $updatedMolContribution = $this->molContributionRepo->update($fakeMolContribution, $molContribution->id);

        $this->assertModelData($fakeMolContribution, $updatedMolContribution->toArray());
        $dbMolContribution = $this->molContributionRepo->find($molContribution->id);
        $this->assertModelData($fakeMolContribution, $dbMolContribution->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mol_contribution()
    {
        $molContribution = factory(MolContribution::class)->create();

        $resp = $this->molContributionRepo->delete($molContribution->id);

        $this->assertTrue($resp);
        $this->assertNull(MolContribution::find($molContribution->id), 'MolContribution should not exist in DB');
    }
}
