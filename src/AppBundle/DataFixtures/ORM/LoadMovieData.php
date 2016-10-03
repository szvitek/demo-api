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
        $movies = $this->container->get('csv.manager')->read();

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

}