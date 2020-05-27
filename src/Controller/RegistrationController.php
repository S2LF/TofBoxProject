<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("register")
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/", name="app_register")
     * 
     * Display registration form
     * Create User
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            )->setDateCreation(new \Datetime('now', new \DateTimeZone('Europe/Paris')  ))
            ->setPhotoProfil('default/default.jpg');
            

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/terms-of-service", name="app_terms")
     * 
     * Display terms
     */
    public function terms()
    {
        return $this->render('registration/termsOfService.html.twig');
    }

    /**
     * @Route("/privacy-policy", name="app_policy")
     * 
     * Display policy
     */
    public function policy()
    {
        return $this->render('registration/privacyPolicy.html.twig');
    }
}


