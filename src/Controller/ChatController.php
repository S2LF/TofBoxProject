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
     * @Route("/", name="chat")
     */
    public function index()
    {
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
        ]);
    }

    //TODO Rename create in show
    // 1. Show if exist
    // 2. On write, if exist->push else ->createArray


    /**
     * @Route("/create/{id}", name="create_chat")
     */
    public function createChat(User $user, Request $request, EntityManagerInterface $em){

    // On récupère les 2 Users
    $currentUser = $this->getUser();
    $user2Id = $request->attributes->get('id');
    $user2 = $em->getRepository(User::class)->findOneBy(['id' => $user2Id]);


    $directory_path = $this->getParameter('private_directory')."".$currentUser->getId()."-".$user2->getId();
    $file_name = $currentUser->getId()."-".$user2->getId();
    $file_path = $directory_path.'/'.$file_name.'.txt';

        // Lecture & affichage
        if(file_exists($file_path)){
            $lines = file_get_contents($file_path);
            var_dump($lines);
            $lines = explode("|", $lines);
            var_dump($lines);
        } else {
            $lines = ["la conversation est vide"];
        }

    return $this->render('chat/index.html.twig',
    [
        'lines' => $lines,
        'currentUser' => $currentUser,
        'user2' => $user2
    ]);

    }

    /**
     * @Route("/add/{id}", name="chat_add_message")
     */
    public function addMessage(User $user, Request $request, EntityManagerInterface $em)
    {

        // On récupère les 2 Users
        $currentUser = $this->getUser();
        $user2Id = $request->attributes->get('id');
        $user2 = $em->getRepository(User::class)->findOneBy(['id' => $user2Id]);


        // Ecriture et sauvegarde

        // Si le texte est envoyé et pas vide
        if($text = $request->request->get('text')){


            $directory_path= $this->getParameter('private_directory').''.$currentUser->getId()."-".$user2->getId();
            $file_name = $currentUser->getId()."-".$user2->getId();

            $file_path = $directory_path.'/'.$file_name.'.php';
            // Si le dossier n'existe pas déjà
            if(!\file_exists($directory_path)){
                // On crée le dossier dans lequel sera stocké leur conversation
                \mkdir($directory_path);
            }

            // // On crée une nouvelle entrée dans Chat;
            // $chat = new Chat();
            // $chat->setDateCreation(new DateTime());
            // $chat->setChatContent($file_path);

            // $chat->addUser($currentUser);
            // $chat->addUser($user2);

            // $em->persist($chat);
            // $em->flush();


            // $new = $_POST['text'];

            // $date = new DateTime('now');
            // $date = $date->format('H\hi');

            // On remplace le saut à la ligne par des <br>
            // Le texte est stocké sur 1 seul ligne
            $textSafe = trim(preg_replace('/\s\s+/', '<br>', $text));
           

            $array = [$currentUser->getNickname()."|$textSafe\n"];
            // var_dump($array);

            // // Pour écrire et saut à la ligne automatique
             file_put_contents($file_path, $array, FILE_APPEND );
            // var_dump(file_put_contents($file_path, $array, FILE_APPEND ));
        }

        // Lecture & affichage
        if(file_exists($file_path)){
            $file = fopen($file_path, 'r');

            $lines = file($file_path);

            fclose($file);

        } else {
            $lines = "la conversation est vide";
        }

        return $this->render('chat/index.html.twig',
        [
            'lines' => $lines,
            'currentUser' => $currentUser,
            'user2' => $user2
        ]);


        

    }

    // TODO ?
    public function edit()
    {}

    public function delete()
    {}
}