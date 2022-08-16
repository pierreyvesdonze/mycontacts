<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\CategoryType;
use App\Form\ContactType;
use App\Repository\CategoryRepository;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
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

    #[Route('/contacts/demo', name: 'contacts_demo')]
    public function indexDemo(
        ContactRepository $contactRepository,
        CategoryRepository $categoryRepository,
        Request $request,
        UserRepository $userRepository
    ): Response {
        $user = $userRepository->findOneBy([
            'id' => 4
        ]);

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

        return $this->render('contacts/demo.index.html.twig', [
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
            $contact->setLastName(ucfirst($form->get('lastName')->getData()));
            $contact->setFirstName(ucfirst($form->get('firstName')->getData()));
            
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

    #[Route('/contact/demo/{id}', name: 'contact_demo')]
    public function contactDemo(Contact $contact)
    {
        return $this->render('contacts/demo.contact.html.twig', [
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
            $contact->setLastName(ucfirst($form->get('lastName')->getData()));
            $contact->setFirstName(ucfirst($form->get('firstName')->getData()));
            $this->em->flush();

            $this->addFlash('success', 'Le contact a bien été modifié');

            return $this->redirectToRoute('contact', [
                'id' => $contact->getId()
            ]);
        }

        return $this->render('contacts/_form.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
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

    #[Route('/contacts/export', name: 'contacts_export')]
    public function contactsExport(
        CategoryRepository $categoryRepository,
        ContactRepository $contactRepository,
        Request $request
    )
    {
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

            $contactsArr = [];
            foreach ($contacts as $contact) {
                $contactsArr[] = $contact->getEmail();
            }

            $filename = "contacts.csv";
            $f = fopen($filename, 'w');
            fputcsv($f, $contactsArr);
            fclose($f);

            $response = new Response();

            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', mime_content_type($filename));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
            $response->headers->set('Content-length', filesize($filename));

            $response->sendHeaders();

            $response->setContent(file_get_contents($filename));

            return $response;
        } 
        
        return $this->render('contacts/export.html.twig', [
            'form' => $filterForm->createView()
        ]);
    }

    #[Route('/contacts/export/demo', name: 'contacts_export_demo')]
    public function contactsExportDemo(
        CategoryRepository $categoryRepository,
        ContactRepository $contactRepository,
        UserRepository $userRepository,
        Request $request
    ) {
        $user = $userRepository->findOneBy([
            'id' => 4
        ]);

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

            $contactsArr = [];
            foreach ($contacts as $contact) {
                $contactsArr[] = $contact->getEmail();
            }

            $filename = "contacts.csv";
            $f = fopen($filename, 'w');
            fputcsv($f, $contactsArr);
            fclose($f);

            $response = new Response();

            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', mime_content_type($filename));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
            $response->headers->set('Content-length', filesize($filename));

            $response->sendHeaders();

            $response->setContent(file_get_contents($filename));

            return $response;
        }

        return $this->render('contacts/export.html.twig', [
            'form' => $filterForm->createView()
        ]);
    }
}
