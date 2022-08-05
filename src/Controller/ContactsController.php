<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\CategoryType;
use App\Form\ContactType;
use App\Repository\CategoryRepository;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ContactsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/contacts', name: 'contacts')]
    public function index(
        ContactRepository $contactRepository,
        CategoryRepository $categoryRepository,
        Request $request
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $filterForm = $this->createForm(CategoryType::class);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $categoryName = $filterForm->get('title')->getData();
            $category = $categoryRepository->findOneBy([
                'title' => $categoryName
            ]);

            $contacts = $contactRepository->findByUserAndCategory($user, $category);

            if ('tous' === $categoryName) {
                $contacts = $contactRepository->findByUser($user);
            }
        } else {
            $contacts = $contactRepository->findByUser($user);
        }

        return $this->render('contacts/index.html.twig', [
            'contacts' => $contacts,
            'form' => $filterForm->createView()
        ]);
    }

    #[Route('/contact/create', name: 'contact_create')]
    public function contactCreate(Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $contact = new Contact;

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contact->setUser($user);
            $this->em->persist($contact);
            $this->em->flush();

            $this->addFlash('success', 'Le contact a bien été créé');

            return $this->redirectToRoute('contact', [
                'id' => $contact->getId()
            ]);
        }

        return $this->render('contacts/_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/contact/{id}', name: 'contact')]
    public function contact(Contact $contact)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        return $this->render('contacts/contact.html.twig', [
            'contact' => $contact
        ]);
    }


    #[Route('/contact/edit/{id}', name: 'contact_edit')]
    public function contactEdit(
        Contact $contact,
        Request $request
    ) {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Le contact a bien été modifié');

            return $this->redirectToRoute('contact', [
                'id' => $contact->getId()
            ]);
        }

        return $this->render('contacts/_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/contact/delete/{id}', name: 'contact_delete')]
    public function contactDelete(Contact $contact) {

        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $this->em->remove($contact);
        $this->em->flush();

        $this->addFlash('success', 'Le contact a bien été supprimé');

        return $this->redirectToRoute('contacts');
    }
}
