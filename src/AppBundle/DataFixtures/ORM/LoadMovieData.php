<?php
/**
 * Created by PhpStorm.
 * User: Szvitek
 * Date: 2016. 10. 02.
 * Time: 22:52
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Movie;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMovieData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        //$file = $this->container->getParameter('data_source');

        //$csv = array_map('str_getcsv', file($file));

        //$rows = array_map('AppBundle\DataFixtures\ORM\LoadMovieData::str_getcsv', file($file));
        //$header = array_shift($rows);

        $movies = $this->readData();

        //var_dump($movies);die;

        foreach($movies as $movie){
            $entity = new Movie();
            $entity->setTitle($movie['title']);
            $entity->setDate(new \DateTime($movie['date']));
            $entity->setGenre($movie['genre']);
            $entity->setMainCharacter($movie['mainChar']);
            $manager->persist($entity);
        }


        $manager->flush();
    }

    private function readData()
    {
        $source = $this->container->getParameter('data_source');
        if (($file = fopen($source, "r")) !== FALSE) {

            $headers = fgetcsv($file,1000,';');
            $movies = array();

            while( $row = fgetcsv($file,1000, ';') )
            {
                $movies[$row[0]] = array_combine($headers,$row);
            }

            fclose($file);
        }

        return $movies;
    }
}