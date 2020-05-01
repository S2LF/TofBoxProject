<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Report;
use App\Form\RetourXPType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/report")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/", name="ajax_signal")
     */
    public function index(Request $request)
    {

            $photoId = $request->request->get("photoId");

            $html = $this->renderView("photo/ajaxReport.html.twig", [
                "photoId" => $photoId,
            ]);
            return new Response($html);

    }

    /**
     * @Route("/ajax/add_report", name="ajax_add_report")
     * @IsGranted("ROLE_USER")
     */
    public function add_report(Request $request, EntityManagerInterface $em)
    {

        $commentReport = $request->request->get("commentReport");
        $select = $request->request->get("select");
        $photoId = $request->request->get("photoId");
        $photo = $em->getRepository(Photo::class)->findOneBy(['id' => $photoId]);
        $user = $this->getUser();


        if($select){
            $report = new Report;

            $report->setDateReport(new \DateTime('now', new \DateTimeZone('Europe/Paris') ));
            $report->setType($select);
            $report->setCommentary($commentReport);
            $report->setStatus(true);
            $report->setPhoto($photo);
            $report->setUser($user);
            
            $em->persist($report);
            $em->flush();

            $this->addFlash('success', 'Votre signalement a bien été enregistré. Merci');
        }

        return $this->redirectToRoute('ajax_show', [
            'photoid' => $photoId
        ]);
    }

    /**
     * @Route("/checked/{id}", name="report_checked")
     * @IsGranted("ROLE_ADMIN")
     */
    public function report_checked(Report $report, EntityManagerInterface $em )
    {

        if($report->getStatus() == true){
            $report->setStatus(false);
        } else{
            $report->setStatus(true);
        }

        $em->flush();

        $this->addFlash('success', 'Action bien prise en compte');
        return $this->redirectToRoute('admin_reports');

    }

    /**
     * @Route("/retourxp/form/{id}", name="retour_form")
     */
    public function retour_form(User $user, Request $request)
    {

        $form = $this->createForm(RetourXPType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $now = new DateTime('now');
           $file =  new FileSystem;
           $path = $this->getParameter('private_directory').'retourxp/'.$this->getUser()->getId().'-Date-'.$now->format('d-m-Y_H-i-s').'.doc';

            $content = "Retour d'expérience".PHP_EOL;
            $content .= PHP_EOL;
            $content .= "I. Expérience utilisateur";
            $content .= PHP_EOL;
            $content .= "Globalement, comment jugeriez-vous votre découverte et visite du site ?".PHP_EOL;
            $content .= $form->get('juger')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Votre expérience a-t-elle été fluide ?'.PHP_EOL;
            $content .= $form->get('fluide')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Est-ce que les boutons sont bien situés (pour vous) ?'.PHP_EOL;
            $content .= $form->get('boutons')->getData().PHP_EOL;
            $content .= 'Auriez-vous des propositions ?'.PHP_EOL;
            $content .= $form->get('xp_propositions')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= "II. Photos";
            $content .= PHP_EOL;
            $content .= 'La manière de voir les photos vous parait-elle bien ?'.PHP_EOL;
            $content .= $form->get('photo')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= "La manière d'ajouter une photo vous parait-elle bien ?".PHP_EOL;
            $content .= $form->get('upload')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Le système de like vous paraît-il bien ?'.PHP_EOL;
            $content .= $form->get('like')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Auriez-vous des propositions ?'.PHP_EOL;
            $content .= $form->get('photo_propositions')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= "III. Espace utilisateur";
            $content .= PHP_EOL;
            $content .= 'Votre espace utilisateur est-il ergonomique ?'.PHP_EOL;
            $content .= $form->get('user')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Le système de follow vous paraît-il bien ?'.PHP_EOL;
            $content .= $form->get('follow')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Auriez-vous des propositions ?'.PHP_EOL;
            $content .= $form->get('user_propositions')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= "IV. Design";
            $content .= PHP_EOL;
            $content .= 'Le site est-il agréable à regarder ?'.PHP_EOL;
            $content .= $form->get('design')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Le site met-il les photos en valeur ?'.PHP_EOL;
            $content .= $form->get('valeur')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Auriez-vous des propositions ?'.PHP_EOL;
            $content .= $form->get('design_propositions')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= "V. RGPD";
            $content .= PHP_EOL;
            $content .= 'Avez-vous lu les RGPD ?'.PHP_EOL;
            $content .= $form->get('rgpd')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Si oui, est-ce que les termes vous paraissent claire ?'.PHP_EOL;
            $content .= $form->get('claire')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= 'Auriez-vous des propositions ?'.PHP_EOL;
            $content .= $form->get('rgpd_propositions')->getData().PHP_EOL;
            $content .= PHP_EOL;
            $content .= "VI. Suggestions";
            $content .= PHP_EOL;
            $content .= 'Auriez-vous des propositions pour améliorer le site ?'.PHP_EOL;
            $content .= $form->get('sug_propositions')->getData().PHP_EOL;
            $content .= PHP_EOL;



           $file->dumpFile($path, $content);

            
            return $this->redirectToRoute('home');
        }




        return $this->render('report/retoursurXP.html.twig', [
            'form' => $form->createView()
        ]);


    }
}
