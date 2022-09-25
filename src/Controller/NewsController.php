<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\News;
use App\Pagination\Paginator;
use App\Repository\NewsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends AbstractController
{     

	#[Route('/news', defaults: ['page' => '1', '_format' => 'html'], methods: ['GET'], name: 'news_index')]
    #[Route('/news/page/{page<[1-9]\d*>}', defaults: ['_format' => 'html'], methods: ['GET'], name: 'news_index_paginated')]
    #[Cache(smaxage: 10)]
    public function index(int $page,ManagerRegistry $doctrine,NewsRepository $news){
         
    	$paginator=$doctrine->getRepository(News::class)->findAll();
          
    	//$latestPosts = $news->findLatest($page);

    	return $this->render('list.html.twig', ['paginator' => $paginator]);
    }
    
    #[Route('/delete_news', name: 'news_delete')]
    public function delete(ManagerRegistry $doctrine,Request $request){
    	$news=$doctrine->getRepository(News::class)->find($request->request->get('news'));
    	$entityManger = $doctrine->getManager();
    	$entityManger->remove($news);
          $entityManger->flush();

          return $this->redirectToRoute('get_news');

    }
}