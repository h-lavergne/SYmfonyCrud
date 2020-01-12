<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_list")
     */
    public function index()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render("articles/index.html.twig", array('articles' => $articles));
    }


    /**
     * @Route("/article/show/{id}", name="article_show")
     * @param $id
     * @return Response
     */
    public function show($id){
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('articles/show.html.twig', array("article" => $article));
    }


    /**
     * @Route("/article/new", name="new_article")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request){
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                "required" => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('body', TextareaType::class, [
                "required" => false,
                'attr' => ["class" => "form-control"]
            ])
            ->add("save", SubmitType::class, [
                "label" => "Create",
                "attr" => ["class" => "btn btn-primary mt-3"]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/new.html.twig', [
            "form" => $form->createView()
        ]);
    }



    /**
     * @Route("/article/delete/{id}", name="article_delete")
     */
    public function delete($id){
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $em = $this->getDoctrine()->getManager();

        $em->remove($article);
        $em->flush();
        return $this->redirectToRoute("article_list");
    }

    /**
     * @Route("/article/edit/{id}", name="article_update")
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function update(Request $request, $id){
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                "required" => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('body', TextareaType::class, [
                "required" => false,
                'attr' => ["class" => "form-control"]
            ])
            ->add("save", SubmitType::class, [
                "label" => "Update",
                "attr" => ["class" => "btn btn-primary mt-3"]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', [
            "form" => $form->createView(),
            "article" => $article
        ]);
    }



//    /**
//     * @Route("/article/save")
//     */
//    public function save(){
//        $entityManager = $this->getDoctrine()->getManager();
//        $article = new Article();
//        $article->setTitle("Article Two");
//        $article->setBody("This is the body for article Two");
//
//        $entityManager->persist($article);
//        $entityManager->flush();
//
//        return new Response("Saves an article with the id of " . $article->getId());
//    }
}
