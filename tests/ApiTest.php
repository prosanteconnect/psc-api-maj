<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test of Ps get index.
     *
     * @return void
     */
    public function testGetPsList()
    {
        DB::table('ps')->insert([
            'nationalId' => rand(0, 999999999),
            'email' => Str::random(10) . '@gmail.com',
            'phone' => '0611223344',
        ]);
        DB::table('ps')->insert([
            'nationalId' => rand(0, 999999999),
            'email' => 'hellohellohello@gmail.com',
            'phone' => '0000000000',
        ]);
        $response = $this->call('GET', '/api/ps');
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertCount(3, $data);
        $this->assertEquals('06******44', $data[0]->phone);
        $this->assertEquals('hel************@g****.com', $data[1]->email);
    }

    public function testGetPs()
    {
        $this->psSetup();
        $response = $this->call('GET', '/api/ps/123456%2F7891-REF');
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('123456/7891-REF', $data->nationalId);
    }

    public function testPutPs()
    {
        $this->psSetup('PUT');
        $ps = '{"idType":"3","id":"190000042/021721",
        "nationalId":"123456/789","lastName":"PROS","firstName":"JEAN LUC"}';
        $response = $this->call(
            'PUT',
            '/api/ps/123456%2F7891-REF',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $ps
        );
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('123456/7891-REF', $data->nationalId);
        $response = $this->call('GET', '/api/ps/123456%2F7891-REF');
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('JEAN LUC', $data->firstName);
    }

    public function testPutPs2()
    {
        $this->psSetup('PUT');
        $ps = '{"idType":"3","id":"190000042/021721",
        "nationalId":"123456/7891-REF","lastName":"PROS","firstName":"JEAN LUC"}';
        $response = $this->call(
            'PUT',
            '/api/ps',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $ps
        );
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('123456/7891-REF', $data->nationalId);
        $response = $this->call('GET', '/api/ps/123456%2F7891-REF');
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('123456/7891-REF', $data->nationalId);
        $this->assertEquals('JEAN LUC', $data->firstName);
    }

    public function testPutPsWrongAttribute()
    {
        $this->psSetup('PUT');
        $ps = '{"idType":"3","id":"190000042/021721",
        "nationalId":"123456/7891-REF","lastName":"PROS","firstName":"JEAN LUC","fake-attribute":"wrong"}';
        $response = $this->call(
            'PUT',
            '/api/ps',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $ps
        );
        $message = json_decode($response->content())->message;
        $this->assertEquals(500, $response->status());
        $this->assertEquals("l'attribut fake-attribute est illégal.", $message);
    }

    public function testPsGetProfessions() {
        $this->psSetup();
        $response = $this->call('GET', '/api/ps/123456%2F7891-REF/professions');
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('abc456', $data[0]->exProId);
        $this->assertEquals('123def', $data[0]->expertises[0]->expertiseId);
        $this->assertEquals('S', $data[0]->workSituations[0]->situId);
        $this->assertEquals('0000000000000004', $data[0]->workSituations[0]->structures[0]->structureId);
    }

    public function testPsGetProfession() {
        $this->psSetup();
        $response = $this->call('GET', '/api/ps/123456%2F7891-REF/professions/abc456');
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('abc456', $data->exProId);
        $this->assertEquals('123def', $data->expertises[0]->expertiseId);
        $this->assertEquals('S', $data->workSituations[0]->situId);
        $this->assertEquals('0000000000000004', $data->workSituations[0]->structures[0]->structureId);
    }

    private function psSetup($method = 'POST') {
        $ps = '{"idType":"3","id":"190000042/021721",
        "nationalId":"123456/789","lastName":"PROS","firstName":"JEAN LOUIS","dateOfBirth":"today",
        "phone":"0555926000","email":"","salutationCode":"M","professions":[{"code":"abc","categoryCode":"456",
        "lastName":"PROS","firstName":"JEAN LOUIS","expertises":[{"typeCode":"123","code":"def"}],
        "workSituations":[{"modeCode":"S","structures":[{"structureId":"0000000000000004"}]}]}]}';
        $this->call(
            $method,
            '/api/ps',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $ps
        );
        DB::table('psref')->insert([
            'nationalIdRef' => '123456/7891-REF',
            'nationalId' => '123456/789',
            'activated' => '1',
        ]);
    }

    public function testGetStructure()
    {
        $this->structureSetup('PUT');
        $response = $this->call('GET', '/api/structures/666');
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('666', $data->structureTechnicalId);
        $this->assertEquals('97411 ST PAULOtito', $data->cedexOffice);
        $this->assertEquals('0262453535', $data->fax);
    }

    private function structureSetup($method = 'POST') {
        $structure = '{"siteSIRET":"","siteSIREN":"","siteFINESS":"","legalEstablishmentFINESS":"",
        "structureTechnicalId":"666","legalCommercialName":"","publicCommercialName":"","recipientAdditionalInfo":"",
        "geoLocationAdditionalInfo":"","streetNumber":"42","streetNumberRepetitionIndex":"","streetCategoryCode":"CHE",
        "streetLabel":"DU GRAND POURPIER","distributionMention":"","cedexOffice":"97411 ST PAULOtito",
        "postalCode":"97411","communeCode":"97415","countryCode":"","phone":"0262453545","phone2":"","fax":"0262453535",
        "email":"","departmentCode":"","oldStructureId":"1","registrationAuthority":"HIHIHIHIHIHI"}';
        $this->call(
            $method,
            '/api/structures',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $structure
        );
    }

}
