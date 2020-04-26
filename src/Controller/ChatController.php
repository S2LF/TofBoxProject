<?php

namespace App\Controller;

use DateTime;
use App\Entity\Chat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/chat")
 */
class ChatController extends AbstractController
{
    /**
     * @Route("/{id}", name="chat")
     */
    public function index(User $user, Request $request, EntityManagerInterface $em)
    {
    $userId = $request->attributes->get('id');
    $user2 = $em->getRepository(User::class)->findOneBy(['id' => $userId]);
    $currentUser = $this->getUser();

    


        return $this->render('chat/index.html.twig', [
            'currentUser' => $currentUser,
            'user2' => $user2
        ]);
    }

    //TODO Rename create in show
    // 1. Show if exist
    // 2. On write, if exist->push else ->createArray


    // /**
    //  * @Route("/create/{id}", name="create_chat")
    //  */
    // public function showChat(User $user, Request $request, EntityManagerInterface $em){

    // // On récupère les 2 Users
    // $currentUser = $this->getUser();
    // $user2Id = $request->attributes->get('id');




    // }

    /**
     * @Route("/add/{id}", name="chat_add_message")
     */
    public function addMessage(User $user, Request $request, EntityManagerInterface $em)
    {
        // On récupère les 2 Users
        $currentUser = $this->getUser();
        $user2Id = $request->attributes->get('id');
        $user2 = $em->getRepository(User::class)->findOneBy(['id' => $user2Id]);

        $chats = $currentUser->getChats();

        if(!$chats->getUsers()->contains($user2)){

            $chat = new Chat;

            $chat->addUser($currentUser);
            $chat->addUser($user2);
            $chat->setDateCreation(new \DateTime('now', new \DateTimeZone('Europe/Paris') ));
            $chat->setChatContent('test');

            $em->persist($chat);
            $em->flush();
        }


        return $this->redirectToRoute('chat', ['id' => $user2Id] );


    // Si pas déjà chat on crée chat.
    // On addChat les 2 User 
    // 

    }

    // TODO
    public function delete()
    {}
}