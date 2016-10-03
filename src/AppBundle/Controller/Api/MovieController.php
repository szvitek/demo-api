<?php
/**
 * Created by PhpStorm.
 * User: Szvitek
 * Date: 2016. 10. 03.
 * Time: 2:27
 */

namespace AppBundle\Controller\Api;


use AppBundle\Entity\Movie;
use AppBundle\Form\MovieType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $movie = new Movie();

        $form = $this->createForm(MovieType::class, $movie);
        $form->submit($data);

        $movie->setDate(new \DateTime($data['date']));

        $em = $this->getDoctrine()->getManager();
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

    /**
     * @Route("/api/movies/{id}", name="api_movie_update")
     * @Method({"PUT", "PATCH"})
     */
    public function updateAction(Request $request, $id)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        $movie = $this->getDoctrine()->getRepository('AppBundle:Movie')->find($id);

        if (!$movie) {
            throw $this->createNotFoundException('No movie found for id: '.$id);
        }

        $form = $this->createForm(MovieType::class, $movie);
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);

        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        $data = $this->serializeMovie($movie);
        $response = new JsonResponse($data, 200);

        return $response;
    }

    /**
     * @Route("/api/movies/{id}")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $movie = $this->getDoctrine()->getRepository('AppBundle:Movie')->find($id);

        if ($movie) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($movie);
            $em->flush();
        }

        return new Response(null,204);
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