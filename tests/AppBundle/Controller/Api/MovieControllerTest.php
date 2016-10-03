<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Entity\Movie;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    public function testPOST()
    {
        $client = static::createClient();

        $container = self::$kernel->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $movieRepo = $em->getRepository('AppBundle:Movie');
        $movieRepo->createQueryBuilder('movie')
            ->delete()
            ->getQuery()
            ->execute();

        $data = array(
            'title' => 'Snatch',
            'date' => '2000-09-01',
            'genre' => 'Comedy',
            'mainChar' => 'Jason Statham'
        );

        $client->request(
            'POST',
            '/api/movies',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($data)
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->has('Location'));
        $finishedData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('title', $finishedData);
        $this->assertEquals('Jason Statham', $data['mainChar']);
    }

    public function testGETMovie()
    {
        $client = static::createClient();

        /** @var EntityManager $em */
        $em = self::$kernel->getContainer()->get('doctrine')->getManager();
        $movieRepo = $em->getRepository('AppBundle:Movie');
        $movieRepo->createQueryBuilder('movie')
            ->delete()
            ->getQuery()
            ->execute();

        $movie = new Movie();
        $movie->setTitle('Avatar');
        $movie->setDate(new \DateTime('2009-12-17'));
        $movie->setGenre('Fantasy');
        $movie->setMainCharacter('Sam Worthington');

        $em->persist($movie);
        $em->flush();

        $client->request('GET', '/api/movies/'.$movie->getId());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(
            array(
                'title',
                'slug',
                'date',
                'genre',
                'mainCharacter'
            ),
            array_keys($data)
        );
    }

    public function testGETMoviesCollection()
    {
        $client = static::createClient();

        /** @var EntityManager $em */
        $em = self::$kernel->getContainer()->get('doctrine')->getManager();
        $movieRepo = $em->getRepository('AppBundle:Movie');
        $movieRepo->createQueryBuilder('movie')
            ->delete()
            ->getQuery()
            ->execute();

        $movie1 = new Movie();
        $movie1->setTitle('Snatch');
        $movie1->setDate(new \DateTime('2000-09-01'));
        $movie1->setGenre('Comedy');
        $movie1->setMainCharacter('Jason Statham');
        $em->persist($movie1);

        $movie2 = new Movie();
        $movie2->setTitle('Avatar');
        $movie2->setDate(new \DateTime('2009-12-17'));
        $movie2->setGenre('Fantasy');
        $movie2->setMainCharacter('Sam Worthington');
        $em->persist($movie2);

        $movie3 = new Movie();
        $movie3->setTitle('Scarface');
        $movie3->setDate(new \DateTime('1984-02-10'));
        $movie3->setGenre('Drama');
        $movie3->setMainCharacter('Al Pacino');
        $em->persist($movie3);

        $em->flush();

        $client->request('GET', '/api/movies');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertInternalType('array', $data['movies']);
        $this->assertEquals(3, count($data['movies']));
        $this->assertEquals('Snatch', $data['movies'][0]['title']);
        $this->assertEquals('Fantasy', $data['movies'][1]['genre']);
        $this->assertEquals('Al Pacino', $data['movies'][2]['mainCharacter']);
    }
}
