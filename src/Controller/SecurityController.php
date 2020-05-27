<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeMailType;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * 
     * Display login form
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     * 
     * Logout connected User
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/forgottenPassword", name="app_forgotten_password")
     * 
     * Display forgottent password form
     * Send email
     */
    public function forgottenPassword( Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        if($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneByEmail($email);

            /* @var $user User */

            if($user === null){
                $this->addFlash('error', 'Email inconnu');
                return $this->redirectToRoute('home');
            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('home');
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Forgot Password'))
                    ->setFrom('no-reply@tofbox.fr')
                    ->setTo($user->getEmail())
                    ->setBody(
                        "Bonjour, voici le lien pour réinitialiser votre mot de passe : ". $url,
                        'text/html'
                    );
            $mailer->send($message);

            $this->addFlash('success', 'Un mail vous a été envoyé pour réinitialiser votre mot de passe.');
        }
        return $this->render('security/forgotten_password.html.twig');

    }

    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     * 
     * Check the token
     * Display reset password form
     * Encode & Change password in DB
     */

     public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
     {

        if($request->isMethod('POST') ){
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findOneByResetToken($token);
            /* @var $user User */

            if($user === null){
                $this->addFlash('error', 'Token inconnu');
                return $this->redirectToRoute('home');
            }
            // Si les 2 mots de passes sont identique
            if($request->request->get('password') == $request->request->get('repeatPassword')){
                $user->setResetToken(null);
                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
                $em->flush();

                $this->addFlash('success', 'Mot de passe mis à jours');
                return $this->redirectToRoute('home');
            } else {
                $this->addFlash('error', 'Les mots de passes ne sont pas identique');
                return $this->redirectToRoute('home');
            }


        } else {

            return $this->render('security/reset_password.html.twig', ['token' => $token ]);
        }

     }

     /**
      * @Route("/change_password/{id}", name="app_change_password")
      * 
      * Check if User can change password
      * Display form
      * Encode & Change password in DB
      */
      public function changePassword(Request $request, EntityManagerInterface $em, User $user, UserPasswordEncoderInterface $passwordEncoder)
      {
        if($this->getUser() == $user || $this->isGranted('ROLE_ADMIN')){

            $form = $this->createForm(ChangePasswordType::class, $user);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {


                $user->setPassword($passwordEncoder->encodePassword($user, $form->get('newPassword')->getData()));
                $em->flush();

                $this->addFlash('success', 'Mot de passe mis à jours');
                return $this->redirectToRoute('profil', ['id' => $user->getId()]);

            } else {
                // On envoie vers le form
                return $this->render('security/change_password.html.twig', [ 
                    'user' => $user,
                    'form' => $form->createView() ]);
            }
        } else {
            $this->addFlash('error', "Vous n'êtes pas autorisé à faire cela");
        }
        return $this->redirectToRoute('profil', ['id' => $user->getId()]);
        
      }

      /**
      * @Route("/change_email/{id}", name="app_change_email")
      *
      * Check if User can change an Email
      * Display form
      * Check if new email already exist in DB 
      * Change email in DB
      * Logout
      */
      public function changeEmail(Request $request, EntityManagerInterface $em, User $user, UserRepository $urepo)
      {
        if($this->getUser() == $user || $this->isGranted('ROLE_ADMIN')){

            $form = $this->createForm(ChangeMailType::class, $user);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                if($urepo->isAlreadyExistEmail($form->get('email')->getData()) == 0){
                    $user->setEmail($form->get('email')->getData());
                    $em->flush();
                    $this->addFlash('success', 'Votre email a bien été mis à jours');
                    return $this->redirectToRoute('app_logout');
                }

                return $this->redirectToRoute('profil', ['id' => $user->getId()]);
            } else {
                $this->addFlash('error', 'Cet email est déjà utilisé !');
                // On envoie vers le form
                return $this->render('security/change_mail.html.twig', [ 
                    'user' => $user,
                    'form' => $form->createView() ]);
            }
        } else {
            $this->addFlash('error', "Vous n'êtes pas autorisé à faire cela");
        }
  
      }
}
