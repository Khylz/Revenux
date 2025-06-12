<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Form\ExpenseType;
use App\Form\ExpenseCategoryType;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/expense')]
#[IsGranted('ROLE_USER')]
class ExpenseController extends AbstractController
{
    #[Route('/', name: 'app_expense_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ExpenseRepository $expenseRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $expenses = $expenseRepository->findByUserExpenses($user);
        
        // Créer le formulaire pour une nouvelle dépense
        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense, [
            'user' => $user
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$request->request->has('edit_id')) {
            $expense->setUser($user);
            $expense->setCreationDate(new \DateTimeImmutable());
            $entityManager->persist($expense);
            $entityManager->flush();

            $this->addFlash('success', 'La dépense a été créée avec succès.');
            return $this->redirectToRoute('app_expense_index');
        }

        // Créer le formulaire pour une nouvelle catégorie
        $category = new ExpenseCategory();
        $categoryForm = $this->createForm(ExpenseCategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $category->setUser($user);
            $category->setCreationDate(new \DateTimeImmutable());
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès.');
            return $this->redirectToRoute('app_expense_index');
        }

        // Traitement des formulaires d'édition
        if ($request->request->has('edit_id')) {
            $editId = $request->request->get('edit_id');
            $expenseToEdit = $expenseRepository->find($editId);
            
            if ($expenseToEdit && $expenseToEdit->getUser() === $user) {
                $editForm = $this->createForm(ExpenseType::class, $expenseToEdit, [
                    'user' => $user
                ]);
                $editForm->handleRequest($request);
                
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $expenseToEdit->setModificationDate(new \DateTimeImmutable());
                    $entityManager->flush();
                    $this->addFlash('success', 'La dépense a été modifiée avec succès.');
                    return $this->redirectToRoute('app_expense_index');
                }
            }
        }

        // Créer les formulaires d'édition pour chaque dépense
        $editForms = [];
        foreach ($expenses as $expense) {
            $editForms[$expense->getId()] = $this->createForm(ExpenseType::class, $expense, [
                'action' => $this->generateUrl('app_expense_index'),
                'user' => $user
            ])->createView();
        }

        return $this->render('expense/index.html.twig', [
            'expenses' => $expenses,
            'form' => $form->createView(),
            'categoryForm' => $categoryForm->createView(),
            'editForms' => $editForms,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_show', methods: ['GET'])]
    public function show(Expense $expense): Response
    {
        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette dépense.');
        }

        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {
        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette dépense.');
        }

        $form = $this->createForm(ExpenseType::class, $expense, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expense->setModificationDate(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'La dépense a été modifiée avec succès.');
            return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('expense/edit.html.twig', [
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_delete', methods: ['POST'])]
    public function delete(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {
        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette dépense.');
        }

        if ($this->isCsrfTokenValid('delete'.$expense->getId(), $request->request->get('_token'))) {
            $entityManager->remove($expense);
            $entityManager->flush();
            $this->addFlash('success', 'La dépense a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_expense_index');
    }
}
