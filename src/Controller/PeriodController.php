<?php

namespace App\Controller;

use App\Entity\Period;
use App\Form\PeriodType;
use App\Repository\PeriodRepository;
use App\Repository\IncomeRepository;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/period')]
#[IsGranted('ROLE_USER')]
class PeriodController extends AbstractController
{
    #[Route('/', name: 'app_period_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PeriodRepository $periodRepository, IncomeRepository $incomeRepository, ExpenseRepository $expenseRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $periods = $periodRepository->findByUser($user);
        
        $editForms = [];
        $periodsData = [];
        foreach ($periods as $period) {
            $totalIncome = $incomeRepository->getTotalIncomeByPeriodAndUser($period, $user);
            $totalExpense = $expenseRepository->getTotalExpenseByPeriodAndUser($period, $user);
            $balance = $totalIncome - $totalExpense;

            $periodsData[] = [
                'period' => $period,
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'balance' => $balance,
            ];

            $editForms[$period->getId()] = $this->createForm(PeriodType::class, $period, [
                'action' => $this->generateUrl('app_period_index')
            ])->createView();
        }

        // Création d'une nouvelle période
        $period = new Period();
        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$request->request->has('edit_id')) {
            $period->setUser($user);
            $period->setCreationDate(new \DateTimeImmutable());
            $entityManager->persist($period);
            $entityManager->flush();
            $this->addFlash('success', 'La période a été créée avec succès.');
            return $this->redirectToRoute('app_period_index');
        }

        // Traitement des formulaires d'édition
        if ($request->request->has('edit_id')) {
            $editId = $request->request->get('edit_id');
            $periodToEdit = $periodRepository->find($editId);
            
            if ($periodToEdit && $periodToEdit->getUser() === $user) {
                $editForm = $this->createForm(PeriodType::class, $periodToEdit);
                $editForm->handleRequest($request);
                
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $entityManager->flush();
                    $this->addFlash('success', 'La période a été modifiée avec succès.');
                    return $this->redirectToRoute('app_period_index');
                }
            }
        }

        return $this->render('period/index.html.twig', [
            'periodsData' => $periodsData,
            'form' => $form->createView(),
            'editForms' => $editForms,
        ]);
    }

    #[Route('/new', name: 'app_period_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $period = new Period();
        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $period->setUser($this->getUser());
            $period->setCreationDate(new \DateTimeImmutable());
            $entityManager->persist($period);
            $entityManager->flush();

            $this->addFlash('success', 'La période a été créée avec succès.');
            return $this->redirectToRoute('app_period_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('period/new.html.twig', [
            'period' => $period,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_period_show', methods: ['GET'])]
    public function show(Period $period): Response
    {
        if ($period->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette période.');
        }

        return $this->render('period/show.html.twig', [
            'period' => $period,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_period_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Period $period, EntityManagerInterface $entityManager): Response
    {
        if ($period->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette période.');
        }

        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La période a été modifiée avec succès.');
            return $this->redirectToRoute('app_period_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('period/edit.html.twig', [
            'period' => $period,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_period_delete', methods: ['POST'])]
    public function delete(Request $request, Period $period, EntityManagerInterface $entityManager): Response
    {
        if ($period->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette période.');
        }

        if ($this->isCsrfTokenValid('delete' . $period->getId(), $request->request->get('_token'))) {
            $entityManager->remove($period);
            $entityManager->flush();
            $this->addFlash('success', 'La période a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_period_index', [], Response::HTTP_SEE_OTHER);
    }
}
