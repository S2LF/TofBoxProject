<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Comment;
use App\Form\AddCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/{id}", name="show_comment")
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        $photoId = $request->attributes->get('id');

        $photo = $em->getRepository(Photo::class)->findOneBy(['id' => $photoId]);

        // $comments = $this->getDoctrine()
        //     ->getRepository(Comment::class)
        //     ->getCommentsFromPhotoId($photo);


        return $this->render('comment/index.html.twig', [
            'photo' => $photo,
        ]);
    }

    /**
     * @Route("/ajax/add", name="add_comment")
     * @IsGranted("ROLE_USER")
     */
    public function add_comment(Request $request, EntityManagerInterface $em)
    {
            $add_comment = $request->request->get("comment");
            $photoId = $request->request->get("photoId");
            $photo = $em->getRepository(Photo::class)->findOneBy(['id' => $photoId]);

            $comment = new Comment();

            if($add_comment) {
                $comment->setComment($add_comment);
                $comment->setDateComment(new \DateTime('now', new \DateTimeZone('Europe/Paris') ));
                $comment->setPhoto($photo);
                $comment->setUser($this->getUser());
                $comment->setIsEdit(false);

                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('show_comment',array('id' => $photo->getId()));
            }

            return $this->redirectToRoute('home');
    }


    /**
     * @Route("/ajax/edit", name="ajax_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit_form(Request $request, EntityManagerInterface $em) 
    {
        
        $commentid = $request->request->get("commentId");
        $comment = $em->getRepository(Comment::class)->findOneBy(['id' => $commentid]);



        if($this->getUser() == $comment->getUser() || $this->getUser()->getRoles('ROLE_ADMIN')){
            $form = $this->createForm(AddCommentType::class, $comment);

            $html = $this->renderView("comment/ajaxEdit.html.twig", [
                'form' => $form->createView(),
                'comment' => $comment
            ]);
            return new Response($html);
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/ajax/edit_form", name="ajax_edit_comment")
     * @IsGranted("ROLE_USER")
     */
    public function edit_comment(Request $request, EntityManagerInterface $em)
    {

        $edit_comment = $request->request->get("comment");
        $commentid = $request->request->get('commentId');
        $comment = $em->getRepository(Comment::class)->findOneBy(['id' => $commentid]);

            if($this->getUser() == $comment->getUser() || $this->getUser()->getRoles('ROLE_ADMIN')){

                if($edit_comment) {
                        $comment->setComment($edit_comment);
                        $comment->setIsEdit(true);
                        $em->flush();

                        return $this->redirectToRoute('show_comment',array('id' => $comment->getPhoto()->getId()));
                    }
            }

            return $this->redirectToRoute('home');

    }

    /**
     * @Route("/ajax/delete", name="delete_comment")
     * @IsGranted("ROLE_USER")
     */
    public function delete_comment(Request $request, EntityManagerInterface $em, CommentRepository $crepo)
    {
        $commentid = $request->request->get('commentid');
        $comment = $em->getRepository(Comment::class)->findOneBy(['id' => $commentid]);
        $photoid = $request->request->get('photoid');
        $photo = $em->getRepository(Photo::class)->findOneBy(['id' => $photoid]);

            if($this->getUser() == $comment->getUser() || $this->getUser()->getRoles('ROLE_ADMIN')){
                $crepo->deleteComment($comment->getId());
                
                $html = $this->renderView("comment/index.html.twig", [
                    'photo' => $photo,
                ]);

                return new Response($html);
            }

        return $this->redirectToRoute('home');
            
    }
}
