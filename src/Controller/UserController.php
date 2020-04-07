<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterEditType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }



    /**
     * @Route("/edit/{id}", name="user_edit")
     * 
     */
    public function edit(User $user, Request $request, EntityManagerInterface $em)
    {
        if( $this->getUser() == $user){
             $form = $this->createForm(RegisterEditType::class, $user);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                if($form->get('photo_profil')->getData()){
                    /** @var UploadedFile $imageFile */
                    $imageFile = $form->get('photo_profil')->getData();
                    // Rename file with SafeName + UniqId
                    $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $user->getNickname());
                    $newFilename = $safeFileName.'-'.uniqid().'.'.$imageFile->guessExtension();

                    // Move the file to the directory public/img
                    try {
                        $path = $user->getId()."/profil";
                        $imageFile->move(
                            // img_directory is define in services.yaml
                            $this->getParameter('img_directory').$path,
                            $newFilename
                        );
                        $user->setPhotoProfil($path."/".$newFilename);
                    } catch( FileException $e) {
                        $this->addFlash("error", "Une problème est survenu lors de l'upload de l'image");
                    }
                }


                $em->persist($user);

                $em->flush();

                $this->addFlash("success", "Votre profil a bien été modifié");
                return $this->redirectToRoute('profil', array('id' => $this->getUser()->getId() ) );
            }
            return $this->render('registration/edition.html.twig', [
                "registrationForm" => $form->createView()
            ]);
        } else {
            $this->addFlash("error", "Accès interdit !");
        }
        return $this->redirectToRoute('home');
       
    }

    public function deleteProfil() // TODO
    {}

    /**
     * @Route("/{id}", name="profil")
     */
    public function userProfil(User $user)
    {

        return $this->render('user/showOne.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("{id}/addfollow/{id_follow}", name="add_follow")
     * id = CurrentUser
     * id_follow = User you want to follow
     */
    public function begin_followUser() // TODO
    {

    }

    /**
     * @Route("{id}/stopfollow/{id_follow}", name="stop_follow")
     * id = CurrentUser
     * id_follow = User you want to follow
     */ 
    public function stop_followUser() // TODO
    {

    }



}
