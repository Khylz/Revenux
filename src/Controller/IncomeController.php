<?php

namespace App\Controller;

use App\Entity\Income;
use App\Entity\IncomeCategory;
use App\Form\IncomeType;
use App\Form\IncomeCategoryType;
use App\Repository\IncomeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/income')]
#[IsGranted('ROLE_USER')]
class IncomeController extends AbstractController
{
    #[Route('/', name: 'app_income_index', methods: ['GET', 'POST'])]
    public function index(Request $request, IncomeRepository $incomeRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $incomes = $incomeRepository->findByUserIncomes($user);
        
        // Créer le formulaire pour un nouveau revenu
        $income = new Income();
        $form = $this->createForm(IncomeType::class, $income, [
            'user' => $user
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$request->request->has('edit_id')) {
            $income->setUser($user);
            $income->setCreationDate(new \DateTimeImmutable());
            $entityManager->persist($income);
            $entityManager->flush();

            $this->addFlash('success', 'Le revenu a été créé avec succès.');
            return $this->redirectToRoute('app_income_index');
        }

        // Créer le formulaire pour une nouvelle catégorie
        $category = new IncomeCategory();
        $categoryForm = $this->createForm(IncomeCategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $category->setUser($user);
            $category->setCreationDate(new \DateTimeImmutable());
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès.');
            return $this->redirectToRoute('app_income_index');
        }

        // Traitement des formulaires d'édition
        if ($request->request->has('edit_id')) {
            $editId = $request->request->get('edit_id');
            $incomeToEdit = $incomeRepository->find($editId);
            
            if ($incomeToEdit && $incomeToEdit->getUser() === $user) {
                $editForm = $this->createForm(IncomeType::class, $incomeToEdit, [
                    'user' => $user
                ]);
                $editForm->handleRequest($request);
                
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $incomeToEdit->setModificationDate(new \DateTimeImmutable());
                    $entityManager->flush();
                    $this->addFlash('success', 'Le revenu a été modifié avec succès.');
                    return $this->redirectToRoute('app_income_index');
                }
            }
        }

        // Créer les formulaires d'édition pour chaque revenu
        $editForms = [];
        foreach ($incomes as $income) {
            $editForms[$income->getId()] = $this->createForm(IncomeType::class, $income, [
                'action' => $this->generateUrl('app_income_index'),
                'user' => $user
            ])->createView();
        }

        return $this->render('income/index.html.twig', [
            'incomes' => $incomes,
            'form' => $form->createView(),
            'categoryForm' => $categoryForm->createView(),
            'editForms' => $editForms,
        ]);
    }

    #[Route('/{id}', name: 'app_income_show', methods: ['GET'])]
    public function show(Income $income): Response
    {
        if ($income->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce revenu.');
        }

        return $this->render('income/show.html.twig', [
            'income' => $income,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_income_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Income $income, EntityManagerInterface $entityManager): Response
    {
        if ($income->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce revenu.');
        }

        $form = $this->createForm(IncomeType::class, $income, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $income->setModificationDate(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Le revenu a été modifié avec succès.');
            return $this->redirectToRoute('app_income_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('income/edit.html.twig', [
            'income' => $income,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_income_delete', methods: ['POST'])]
    public function delete(Request $request, Income $income, EntityManagerInterface $entityManager): Response
    {
        if ($income->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce revenu.');
        }

        if ($this->isCsrfTokenValid('delete'.$income->getId(), $request->request->get('_token'))) {
            $entityManager->remove($income);
            $entityManager->flush();
            $this->addFlash('success', 'Le revenu a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_income_index');
    }
}
