<?php

namespace RobotOyun\OyunBundle;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RobotOyunOyunBundle extends Bundle
{
    public function boot()
    {
        /**
         * @var QueryBuilder $query
         * @var EntityManager $em
         */
        $router = $this->container->get('router');
        $doctrine = $this->container->get('doctrine');
        $event  = $this->container->get('event_dispatcher');

        $em = $doctrine->getManager();
        $query = $em->createQueryBuilder()
            ->from('RobotOyunOyunBundle:Game', 'game')
            ->select('game.slug')
            ->where('game.isActive = :isActive')
            ->setParameter('isActive', true);
        $games = $query->getQuery()->getResult();


        $event->addListener(
            SitemapPopulateEvent::ON_SITEMAP_POPULATE,
            function(SitemapPopulateEvent $event) use ($games, $router){
                //get absolute homepage url

                $url = $router->generate('homepage', array(), true);

                $event->getGenerator()->addUrl(
                    new UrlConcrete(
                        $url,
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_DAILY
                    ),
                    'default'
                );

                foreach ($games as $game) {
                    $url = $router->generate('play_game', array('gameSlug' => $game['slug']), true);

                    $event->getGenerator()->addUrl(
                        new UrlConcrete(
                            $url,
                            new \DateTime(),
                            UrlConcrete::CHANGEFREQ_DAILY
                        ),
                        'games'
                    );
                }

            });
    }
}
