<?php

namespace RobotOyun\OyunBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use RobotOyun\OyunBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /**
         * @var EntityManager $em
         */
        $defaultGamesLength = 12;
        $lazyLoadLength = 6;

        $em = $this->getDoctrine()->getManager();

        $categoryId = $request->query->get('id');
        $sizeOfGames = $request->query->get('len');


        $returnData = array();

        if(!$categoryId && !$sizeOfGames){
            $returnData = array('games' => array());
            $returnData['categories'] = $em->getRepository('RobotOyunOyunBundle:Category')->findBy(array(), array('name' => 'asc'));
            $games = $em->getRepository('RobotOyunOyunBundle:Game')->findBy(array(), array('id' => 'desc'), $defaultGamesLength);
            $gamesArray = array();
            if(sizeof($games)){
                foreach ($games as $game) {
                    $image = $game->getImage();
                    $gamesArray[] = array(
                        'id' => $game->getId(),
                        'link' => $game->getSlug(),
                        'name' => $game->getName(),
                        'thumb' => $image['path'],
                        'url_key' => $game->getSlug()
                    );

                    $returnData['games'] = json_encode($gamesArray);
                }
            }

            return $this->render('RobotOyunOyunBundle:Default:index.html.twig', $returnData);
        }
        elseif($categoryId && $sizeOfGames){
            $category = $em->getRepository('RobotOyunOyunBundle:Category')->find($categoryId);
            $games = $em->getRepository('RobotOyunOyunBundle:Game')->findBy(array('category' =>  $category), array('id' => 'desc'), $lazyLoadLength, $sizeOfGames);
        }
        elseif($categoryId){
            $category = $em->getRepository('RobotOyunOyunBundle:Category')->find($categoryId);
            $games = $em->getRepository('RobotOyunOyunBundle:Game')->findBy(array('category' =>  $category), array('id' => 'desc'), $defaultGamesLength);
        }
        elseif($sizeOfGames)
            $games = $em->getRepository('RobotOyunOyunBundle:Game')->findBy(array(), array('id' => 'desc'), $lazyLoadLength, $sizeOfGames);

        if(sizeof($games)){
            foreach ($games as $game) {
                $image = $game->getImage();
                $returnData[] = array(
                    'id' => $game->getId(),
                    'link' => $game->getSlug(),
                    'name' => $game->getName(),
                    'thumb' => $image['path'],
                    'url_key' => $game->getSlug()
                );
            }
        }

        $response = new Response(json_encode($returnData));
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF8');
        return $response;
    }

    /**
     * @Route("/games/{gameSlug}/", name="play_game")
     */
    public function playGameAction($gameSlug)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('RobotOyunOyunBundle:Category')->findBy(array(), array('name' => 'asc'));
        $game = $em->getRepository('RobotOyunOyunBundle:Game')->findOneBy(array('slug' => $gameSlug));
        return $this->render('RobotOyunOyunBundle:Admin/Game:play.html.twig', array('game' => $game, 'categories' => $categories));

    }

    /**
     * @Route("/search/", name="search")
     */
    public function searchAction(Request $request)
    {
        /**
         * @var EntityManager $em
         * @var Game $game
         * @var QueryBuilder $qb
         */
        $search = $request->query->get('term');
        if(!$search)
            return new Response(json_encode(array()));

        $term = "%" . $search . "%";
        $returnData = array();

        $em = $this->getDoctrine()->getManager();
        $em = $this->getDoctrine()->getEntityManager();

        $query = $em->createQuery("SELECT g FROM RobotOyunOyunBundle:Game g WHERE g.name LIKE :searchterm")->setParameter('searchterm', $term);
        $games = $query->getResult();

        if(sizeof($games)){
            foreach ($games as $game) {
                $image = $game->getImage();
                $returnData[] = array(
                    'id' => $game->getId(),
                    'link' => $game->getSlug(),
                    'name' => $game->getName(),
                    'thumb' => $image['path'],
                    'url_key' => $game->getSlug()
                );
            }
        }

        $response = new Response(json_encode($returnData));
        $response->headers->set('Content-Type', 'application/json');
        $response->setCharset('UTF8');
        return $response;
    }
}