<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FieldMasterApiTest extends TestCase
{
    use MakeFieldMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFieldMaster()
    {
        $fieldMaster = $this->fakeFieldMasterData();
        $this->json('POST', '/api/v1/fieldMasters', $fieldMaster);

        $this->assertApiResponse($fieldMaster);
    }

    /**
     * @test
     */
    public function testReadFieldMaster()
    {
        $fieldMaster = $this->makeFieldMaster();
        $this->json('GET', '/api/v1/fieldMasters/'.$fieldMaster->id);

        $this->assertApiResponse($fieldMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFieldMaster()
    {
        $fieldMaster = $this->makeFieldMaster();
        $editedFieldMaster = $this->fakeFieldMasterData();

        $this->json('PUT', '/api/v1/fieldMasters/'.$fieldMaster->id, $editedFieldMaster);

        $this->assertApiResponse($editedFieldMaster);
    }

    /**
     * @test
     */
    public function testDeleteFieldMaster()
    {
        $fieldMaster = $this->makeFieldMaster();
        $this->json('DELETE', '/api/v1/fieldMasters/'.$fieldMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fieldMasters/'.$fieldMaster->id);

        $this->assertResponseStatus(404);
    }
}
