<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class HelperTest extends AdminTestAbstract
{
    public function testAutocomplete()
    {
        $this->client->request('GET', '/admin/media/autocomplete');

        $response = $this->getLastResponse();
        $this->assertEquals('KO', $response['status']);
        $this->assertEquals('Search string too short', $response['message']);

        $this->client->request('GET', '/admin/media/autocomplete', [
            'q' => 'media'
        ]);

        $response = $this->getLastResponse();
        $this->assertEquals('OK', $response['status']);
        $this->assertEmpty($response['more']);
        $this->assertEmpty($response['items']);

        $this->uploadImage();

        $this->client->request('GET', '/admin/media/autocomplete', [
            'q' => 'monk'
        ]);
        $response = $this->getLastResponse();
        $this->assertEquals('OK', $response['status']);
        $this->assertEmpty($response['more']);
        $this->assertCount(1, $response['items']);

        $this->client->request('GET', '/admin/media/autocomplete', [
            'q' => 'monk',
            'type' => 'image'
        ]);
        $response = $this->getLastResponse();
        $this->assertEquals('OK', $response['status']);
        $this->assertEmpty($response['more']);
        $this->assertCount(1, $response['items']);

        $this->client->request('GET', '/admin/media/autocomplete', [
            'q' => 'monk',
            'type' => 'audio'
        ]);
        $response = $this->getLastResponse();
        $this->assertEquals('OK', $response['status']);
        $this->assertEmpty($response['more']);
        $this->assertCount(0, $response['items']);

        $this->client->request('GET', '/admin/media/autocomplete', [
            'q' => 'monk',
            'provider' => 'image'
        ]);
        $response = $this->getLastResponse();
        $this->assertEquals('OK', $response['status']);
        $this->assertEmpty($response['more']);
        $this->assertCount(1, $response['items']);

        $this->client->request('GET', '/admin/media/autocomplete', [
            'q' => 'monk',
            'provider' => 'soundcloud'
        ]);
        $response = $this->getLastResponse();
        $this->assertEquals('OK', $response['status']);
        $this->assertEmpty($response['more']);
        $this->assertCount(0, $response['items']);
    }

    /**
     * @return mixed
     */
    private function getLastResponse()
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
