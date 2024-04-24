<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        // Fetch all enterprises from the database
        $entreprises = $entrepriseRepository->findBy([],["raisonSociale"=>"ASC"]);
        
        // Render the index page to display the enterprises
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    #[Route('/entreprise/new', name: 'new_entreprise')]
    #[Route('/entreprise/{id}/edit', name: 'edit_entreprise')]
    public function new_edit(Entreprise $entreprise = null , Request $request, EntityManagerInterface $entityManager): Response
    {
        // If no enterprise ID is provided, create a new enterprise instance
        if(!$entreprise) {
            $entreprise = new Entreprise();
        }
      
        // Create a form instance for adding/editing enterprises
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        
        // Handle form submissions
        $form->handleRequest($request);

        // If the form is submitted and valid, persist the enterprise to the database
        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $form->getData();
            $entityManager->persist($entreprise);
            $entityManager->flush();

            // Redirect to the index page after successful submission
            return $this->redirectToRoute('app_entreprise');
        }
    
        // Render the form for adding/editing enterprises
        return $this->render('entreprise/new.html.twig', [
            'formAddEntreprise' => $form,
            'edit' => $entreprise->getId()
        ]);
    }

    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response
    {
        // Render the page displaying details of a specific enterprise
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    #[Route('/entreprise/{id}/delete', name: 'delete_entreprise')]
    public function delete(Entreprise $entreprise, EntityManagerInterface $entityManager)
    {
        // Remove the enterprise from the database
        $entityManager->remove($entreprise);
        $entityManager->flush();

        // Redirect to the index page after successful deletion
        return $this->redirectToRoute('app_entreprise');
    }
}