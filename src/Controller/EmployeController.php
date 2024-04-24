<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(EmployeRepository $employeRepository): Response
    {
        // Fetch all employees from the database
        $employes = $employeRepository->findAll();
        
        // Render the index page to display the employees
        return $this->render('employe/index.html.twig', [
            'employes' => $employes,
        ]);
    }

    #[Route('/employe/new', name: 'new_employe')]
    public function new(Request $request,EntityManagerInterface $entityManager): Response
    {
        // Create a new employee instance
        $employe = new Employe();
        
        // Create a form instance for adding a new employee
        $form = $this->createForm(EmployeType::class, $employe);
        
        // Handle form submissions
        $form->handleRequest($request);

        // If the form is submitted and valid, persist the employee to the database
        if ($form->isSubmitted() && $form->isValid()) {
            $employe = $form->getData();
            $entityManager->persist($employe);
            $entityManager->flush();

            // Redirect to the index page after successful submission
            return $this->redirectToRoute('app_employe');
        }
        
        // Render the form for adding a new employee
        return $this->render('employe/new.html.twig', [
            'formAddemploye' => $form,
        ]);
    }

    #[Route('/employe/{id}', name: 'show_employe')]
    public function show(Employe $employe): Response
    {
        // Render the page displaying details of a specific employee
        return $this->render('employe/show.html.twig', [
            'employe' => $employe,
        ]);
    }
}