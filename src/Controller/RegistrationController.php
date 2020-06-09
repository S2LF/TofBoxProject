<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer): Response
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


            $url = $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL);

            // do anything else you need here, like send an email
            $message = (new \Swift_Message("Confirmation d'inscription"))
            ->setFrom('no-reply@tofbox.fr')
            ->setTo($user->getEmail())
            ->setBody(
                "Bonjour ".$user->getNickname().", <br> merci pour votre inscription à <a target='_blank' href='".$url."'>Tof'Box</a>",
                'text/html'
            );
            $mailer->send($message);

            $this->addFlash('success', "Votre inscription est confirmé. Un mail vous a été envoyé. Bienvenu sur Tof'Box !");

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


