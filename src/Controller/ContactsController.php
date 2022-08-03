<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactsController extends AbstractController
{
    #[Route('/contacts', name: 'contacts')]
    public function index(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findByUser($this->getUser());

        return $this->render('contacts/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }
}
