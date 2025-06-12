<?php

namespace App\Controller;

use App\Entity\ExpenseCategory;
use App\Form\ExpenseCategoryType;
use App\Repository\ExpenseCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/expense/category')]
#[IsGranted('ROLE_USER')]
class ExpenseCategoryController extends AbstractController
{
    #[Route('/', name: 'app_expense_category_index', methods: ['GET'])]
    public function index(ExpenseCategoryRepository $expenseCategoryRepository): Response
    {
        return $this->render('expense_category/index.html.twig', [
            'expense_categories' => $expenseCategoryRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_expense_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $expenseCategory = new ExpenseCategory();
        $expenseCategory->setUser($this->getUser());
        $expenseCategory->setCreationDate(new \DateTimeImmutable());
        
        $form = $this->createForm(ExpenseCategoryType::class, $expenseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($expenseCategory);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès.');
            return $this->redirectToRoute('app_expense_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('expense_category/new.html.twig', [
            'expense_category' => $expenseCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expense_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExpenseCategory $expenseCategory, EntityManagerInterface $entityManager): Response
    {
        if ($expenseCategory->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ExpenseCategoryType::class, $expenseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
            return $this->redirectToRoute('app_expense_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('expense_category/edit.html.twig', [
            'expense_category' => $expenseCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_category_delete', methods: ['POST'])]
    public function delete(Request $request, ExpenseCategory $expenseCategory, EntityManagerInterface $entityManager): Response
    {
        if ($expenseCategory->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$expenseCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($expenseCategory);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_expense_category_index', [], Response::HTTP_SEE_OTHER);
    }
} 