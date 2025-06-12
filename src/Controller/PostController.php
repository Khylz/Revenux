<?php

namespace App\Controller;

use App\Repository\PeriodRepository;
use App\Repository\IncomeRepository;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'app_post')]
    public function index(
        PeriodRepository $periodRepository,
        IncomeRepository $incomeRepository,
        ExpenseRepository $expenseRepository
    ): Response {
        // Récupérer les périodes
        $periods = $periodRepository->findBy([], ['startDate' => 'DESC']);
        
        // Récupérer les revenus et dépenses
        $incomes = $incomeRepository->findBy([], ['incomeDate' => 'DESC']);
        $expenses = $expenseRepository->findBy([], ['expenseDate' => 'DESC']);

        // Calculer les totaux
        $totalIncomes = array_reduce($incomes, function($carry, $income) {
            return $carry + $income->getAmount();
        }, 0);

        $totalExpenses = array_reduce($expenses, function($carry, $expense) {
            return $carry + $expense->getAmount();
        }, 0);

        // Préparer les données pour le graphique des revenus
        $incomeCategories = [];
        foreach ($incomes as $income) {
            $categoryName = $income->getIncomeCategory()->getCategoryName();
            if (!isset($incomeCategories[$categoryName])) {
                $incomeCategories[$categoryName] = 0;
            }
            $incomeCategories[$categoryName] += $income->getAmount();
        }

        // Préparer les données pour le graphique des dépenses
        $expenseCategories = [];
        foreach ($expenses as $expense) {
            $categoryName = $expense->getExpenseCategory()->getCategoryName();
            if (!isset($expenseCategories[$categoryName])) {
                $expenseCategories[$categoryName] = 0;
            }
            $expenseCategories[$categoryName] += $expense->getAmount();
        }

        return $this->render('post/index.html.twig', [
            'periods' => $periods,
            'incomes' => $incomes,
            'expenses' => $expenses,
            'totalIncomes' => $totalIncomes,
            'totalExpenses' => $totalExpenses,
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
        ]);
    }
}
