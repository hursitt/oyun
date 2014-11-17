<?php

namespace RobotOyun\OyunBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testAddcategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'kategori-ekle');
    }

    public function testEditcategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'kategori-duzenle');
    }

    public function testReversecategorystatus()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'durum-degistir');
    }

    public function testAddgame()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'yeni-oyun');
    }

    public function testEditgame()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'oyun-guncelleme');
    }

    public function testReversegamestatus()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'oyun-durumu-degistirme');
    }

}
