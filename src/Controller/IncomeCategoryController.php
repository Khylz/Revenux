<?php

namespace App\Controller;

use App\Entity\IncomeCategory;
use App\Form\IncomeCategoryType;
use App\Repository\IncomeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/income/category')]
#[IsGranted('ROLE_USER')]
class IncomeCategoryController extends AbstractController
{
    #[Route('/', name: 'app_income_category_index', methods: ['GET'])]
    public function index(IncomeCategoryRepository $incomeCategoryRepository): Response
    {
        return $this->render('income_category/index.html.twig', [
            'income_categories' => $incomeCategoryRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_income_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $incomeCategory = new IncomeCategory();
        $incomeCategory->setUser($this->getUser());
        $incomeCategory->setCreationDate(new \DateTimeImmutable());
        
        $form = $this->createForm(IncomeCategoryType::class, $incomeCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($incomeCategory);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès.');
            return $this->redirectToRoute('app_income_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('income_category/new.html.twig', [
            'income_category' => $incomeCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_income_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IncomeCategory $incomeCategory, EntityManagerInterface $entityManager): Response
    {
        if ($incomeCategory->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(IncomeCategoryType::class, $incomeCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
            return $this->redirectToRoute('app_income_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('income_category/edit.html.twig', [
            'income_category' => $incomeCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_income_category_delete', methods: ['POST'])]
    public function delete(Request $request, IncomeCategory $incomeCategory, EntityManagerInterface $entityManager): Response
    {
        if ($incomeCategory->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$incomeCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($incomeCategory);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_income_category_index', [], Response::HTTP_SEE_OTHER);
    }
} 