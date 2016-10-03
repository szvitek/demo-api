<?php
/**
 * Created by PhpStorm.
 * User: Szvitek
 * Date: 2016. 10. 03.
 * Time: 2:27
 */

namespace AppBundle\Controller\Api;


use AppBundle\Entity\Movie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MovieController extends Controller
{
    /**
     * @Route("/api/movies")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $em = $this->getDoctrine()->getManager();

        $movie = new Movie();

        $movie->setTitle($data['title']);
        $movie->setDate(new \DateTime($data['date']));
        $movie->setGenre($data['genre']);
        $movie->setMainCharacter($data['mainChar']);


        $em->persist($movie);
        $em->flush();

        $location = $this->generateUrl('api_movie_show', [
            'id' => $movie->getId()
        ]);
        $data = $this->serializeMovie($movie);
        $response = new JsonResponse($data, 201);
        $response->headers->set('Location', $location);

        return $response;
    }

    /**
     * @Route("/api/movies")
     * @Method("GET")
     */
    public function listAction()
    {
        $movies = $this->getDoctrine()->getRepository('AppBundle:Movie')->findAll();

        $data = [
            'movies' => []
        ];

        foreach ($movies as $movie){
            $data['movies'][] = $this->serializeMovie($movie);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/movies/{id}", name="api_movie_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $movie = $this->getDoctrine()->getRepository('AppBundle:Movie')->find($id);

        if (!$movie) {
            throw $this->createNotFoundException('No movie found for id: '.$id);
        }

        $data = $this->serializeMovie($movie);

        return new JsonResponse($data);

    }

    private function serializeMovie(Movie $movie)
    {
        return [
            'title' => $movie->getTitle(),
            'slug' =>$movie->getSlug(),
            'date' => $movie->getDate(),
            'genre' => $movie->getGenre(),
            'mainCharacter' =>$movie->getMainCharacter()
        ];
    }
}