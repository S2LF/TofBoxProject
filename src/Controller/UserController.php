<?php

namespace App\Controller;

use App\Entity\User;
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
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash("success", "Votre profil a bien été modifié");
            return $this->redirectToRoute("home");
        }
        return $this->render('registration/register.html.twig', [
            "registrationForm" => $form->createView()
        ]);
    }

    public function delete()
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
    public function begin_followUser()
    {

    }

    /**
     * @Route("{id}/stopfollow/{id_follow}", name="stop_follow")
     * id = CurrentUser
     * id_follow = User you want to follow
     */
    public function stop_followUser()
    {

    }



}
