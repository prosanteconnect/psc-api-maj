<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ProfessionApiTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetProfession()
    {
        $this->psSetup();
        $response = $this->call('GET', '/api/ps/123456%2F7891-REF/professions/abc456');
        $data = json_decode($response->content())->data;
        $this->assertEquals(200, $response->status());
        $this->assertEquals('abc456', $data->exProId);
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

}
