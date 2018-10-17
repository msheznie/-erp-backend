<?php

use App\Models\TemplatesGLCode;
use App\Repositories\TemplatesGLCodeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplatesGLCodeRepositoryTest extends TestCase
{
    use MakeTemplatesGLCodeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TemplatesGLCodeRepository
     */
    protected $templatesGLCodeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->templatesGLCodeRepo = App::make(TemplatesGLCodeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTemplatesGLCode()
    {
        $templatesGLCode = $this->fakeTemplatesGLCodeData();
        $createdTemplatesGLCode = $this->templatesGLCodeRepo->create($templatesGLCode);
        $createdTemplatesGLCode = $createdTemplatesGLCode->toArray();
        $this->assertArrayHasKey('id', $createdTemplatesGLCode);
        $this->assertNotNull($createdTemplatesGLCode['id'], 'Created TemplatesGLCode must have id specified');
        $this->assertNotNull(TemplatesGLCode::find($createdTemplatesGLCode['id']), 'TemplatesGLCode with given id must be in DB');
        $this->assertModelData($templatesGLCode, $createdTemplatesGLCode);
    }

    /**
     * @test read
     */
    public function testReadTemplatesGLCode()
    {
        $templatesGLCode = $this->makeTemplatesGLCode();
        $dbTemplatesGLCode = $this->templatesGLCodeRepo->find($templatesGLCode->id);
        $dbTemplatesGLCode = $dbTemplatesGLCode->toArray();
        $this->assertModelData($templatesGLCode->toArray(), $dbTemplatesGLCode);
    }

    /**
     * @test update
     */
    public function testUpdateTemplatesGLCode()
    {
        $templatesGLCode = $this->makeTemplatesGLCode();
        $fakeTemplatesGLCode = $this->fakeTemplatesGLCodeData();
        $updatedTemplatesGLCode = $this->templatesGLCodeRepo->update($fakeTemplatesGLCode, $templatesGLCode->id);
        $this->assertModelData($fakeTemplatesGLCode, $updatedTemplatesGLCode->toArray());
        $dbTemplatesGLCode = $this->templatesGLCodeRepo->find($templatesGLCode->id);
        $this->assertModelData($fakeTemplatesGLCode, $dbTemplatesGLCode->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTemplatesGLCode()
    {
        $templatesGLCode = $this->makeTemplatesGLCode();
        $resp = $this->templatesGLCodeRepo->delete($templatesGLCode->id);
        $this->assertTrue($resp);
        $this->assertNull(TemplatesGLCode::find($templatesGLCode->id), 'TemplatesGLCode should not exist in DB');
    }
}
