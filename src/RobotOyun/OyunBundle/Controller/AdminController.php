<?php

namespace RobotOyun\OyunBundle\Controller;

use Entity\Category;
use RobotOyun\OyunBundle\Form\CategoryType;
use RobotOyun\OyunBundle\Form\GameType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_list_games")
     */
    public function listGamesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $games = $em->getRepository('RobotOyunOyunBundle:Game')->findBy(array(), array('name' => 'asc'));
        $data = array(
            'games' => $games,
            'menu' => 'game'
        );
        return $this->render('RobotOyunOyunBundle:Admin/Game:listGames.html.twig', $data);
    }

    /**
     * @Route("/yeni-oyun", name="admin_add_game")
     */
    public function addGameAction(Request $request)
    {
        $form = $this->createForm(new GameType(), null);
        $form->handleRequest($request);


        print_r($form->get('image')->getData());
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->get("session")->getFlashBag()->add("notice", "Oyun başarıyla eklendi");

            return $this->redirect($this->generateUrl('admin_list_games'));
        }

        $data = array(
            'form' => $form->createView(),
            'menu' => 'game'
        );

        return $this->render('RobotOyunOyunBundle:Admin/Game:addGame.html.twig', $data);

    }

    /**
     * @Route("/oyun-duzenle/{gameSlug}", name="admin_edit_game")
     */
    public function editGameAction($gameSlug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $game = $em->getRepository('RobotOyunOyunBundle:Game')->findOneBy(array('slug' => $gameSlug));
        if(!$game)
            return $this->createNotFoundException('Oyun Bulunamadi');

        $form = $this->createForm(new GameType(), $game);
        $form->handleRequest($request);

        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->get("session")->getFlashBag()->add("notice", "Oyun başarıyla güncellendi.");

            return $this->redirect($this->generateUrl('admin_list_games'));
        }

        $data = array(
            'form' => $form->createView(),
            'menu' => 'game',
            'game' => $game
        );
        return $this->render('RobotOyunOyunBundle:Admin/Game:editGame.html.twig', $data);
    }
    /**
     * @Route("/oyun-sil/{gameSlug}", name="admin_remove_game")
     */
    public function removeGameAction($gameSlug)
    {
        $em = $this->getDoctrine()->getManager();
        $game = $em->getRepository('RobotOyunOyunBundle:Game')->findOneBy(array('slug' => $gameSlug));
        $em->remove($game);
        $em->flush();
        $this->get("session")->getFlashBag()->add("notice", "Oyun başarıyla silindi.");

        return $this->redirect($this->generateUrl('admin_list_games'));
    }

    /**
     * @Route("/oyun-goruntule/{gameSlug}", name="admin_show_game")
     */
    public function showGameAction($gameSlug)
    {
        $em = $this->getDoctrine()->getManager();
        $game = $em->getRepository('RobotOyunOyunBundle:Game')->findOneBy(array('slug' => $gameSlug));
        $data = array(
            'game' => $game,
            'menu' => 'game'
        );
        return $this->render('RobotOyunOyunBundle:Admin/Game:showGame.html.twig', $data);
    }

    /**
     * @Route("/oyun-oyna/{gameSlug}", name="admin_play_game")
     */
    public function playGameAction($gameSlug)
    {
        $em = $this->getDoctrine()->getManager();
        $game = $em->getRepository('RobotOyunOyunBundle:Game')->findOneBy(array('slug' => $gameSlug));
        return new Response($game->getContent());
    }

    /**
     * @Route("/oyun-durumu-degistirme/{gameSlug}/{status}", name="admin_reverse_game_status", options={"expose"=true})
     */
    public function reverseGameStatusAction($gameSlug, $status)
    {
        $em = $this->getDoctrine()->getManager();
        $game = $em->getRepository('RobotOyunOyunBundle:Game')->findOneBy(array('slug' => $gameSlug));

        if($status){
            $game->setIsActive(true);
        }
        else{
            $game->setIsActive(false);
        }
        $em->persist($game);
        $em->flush();
        return new Response($game->getIsActive() ? 'Aktif' : 'Pasif');
    }

    /**
     * @Route("/kategori-ekle", name="admin_add_category")
     */
    public function addCategoryAction(Request $request)
    {
        $form = $this->createForm(new CategoryType(), null);
        $form->handleRequest($request);

        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->get("session")->getFlashBag()->add("notice", "Kategori başarıyla eklendi");
            return $this->redirect($this->generateUrl('admin_list_categories'));
        }

        $data = array(
            'form' => $form->createView(),
            'menu' => 'category'
        );
        return $this->render('RobotOyunOyunBundle:Admin/Category:addCategory.html.twig', $data);
    }

    /**
     * @Route("/kategoriler", name="admin_list_categories")
     */
    public function listCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('RobotOyunOyunBundle:Category')->findBy(array(), array('name' => 'asc'));

        $data = array(
            'categories' => $categories,
            'menu' => 'category'
        );
        return $this->render('RobotOyunOyunBundle:Admin/Category:listCategories.html.twig', $data);
    }
    /**
     * @Route("/kategori-goruntuleme/{categorySlug}", name="admin_show_category")
     */
    public function showCategoryAction($categorySlug)
    {
        /**
         * @var \RobotOyun\OyunBundle\Entity\Category $category
         */
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('RobotOyunOyunBundle:Category')->findOneBy(array('slug' => $categorySlug));

        $data = array(
            'games' => $category->getGames(),
            'menu' => 'category'
        );
        return $this->render('RobotOyunOyunBundle:Admin/Game:listGames.html.twig', $data);
    }

    /**
     * @Route("/kategori-duzenle/{categorySlug}", name="admin_edit_category")
     */
    public function editCategoryAction($categorySlug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('RobotOyunOyunBundle:Category')->findOneBy(array('slug' => $categorySlug));
        if(!$category)
            return $this->createNotFoundException('Kategori Bulunamadi');

        $form = $this->createForm(new CategoryType(), $category);
        $form->handleRequest($request);

        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->get("session")->getFlashBag()->add("notice", "Kategori başarıyla güncellendi");
            return $this->redirect($this->generateUrl('admin_list_categories'));
        }
        $data = array(
            'category' => $category,
            'form' => $form->createView(),
            'menu' => 'category'
        );
        return $this->render('RobotOyunOyunBundle:Admin/Category:editCategory.html.twig', $data);

    }
}
