<?php

namespace App\Controller;

use App\Book\BookManager;
use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Security\Voter\BookVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/book')]
class BookController extends AbstractController
{
    public function __construct(private readonly BookRepository $repository)
    {
    }

    #[Route('', name: 'app_book_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $this->repository->findAll(),
        ]);
    }

    #[Route('/{id<\d+>?1}', name:  'app_book_show', methods: ['GET'])]
    public function show(int $id, BookManager $manager): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $manager->findOne($id),
        ]);
    }

    #[Route('/title/{title}', name: 'app_book_title', methods: ['GET'])]
    public function title(string $title): Response
    {
        $book = $this->repository->findByLikeTitle($title);

        return $this->render('book/show.html.twig', [
            'book' => $book
        ]);
    }

    #[IsGranted('PUBLIC_ACCESS')]
    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    #[Route('/{id<\d+>}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function save(?Book $book, Request $request, EntityManagerInterface $manager): Response
    {
        if ($book) {
            $this->denyAccessUnlessGranted(BookVoter::CREATED, $book);
        }

        $book ??= new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $book->getId()
                && ($user = $this->getUser()) instanceof User) {
                $book->setCreatedBy($user);
            }
            $manager->persist($book);
            $manager->flush();

            return $this->redirectToRoute(
                'app_book_show',
                ['id' => $book->getId()]
            );
        }

        return $this->render('book/save.html.twig', [
            'form' => $form,
        ]);
    }
}
