<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PeriodRepository;
use App\Repository\IncomeRepository;
use App\Repository\ExpenseRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        PeriodRepository $periodRepository,
        IncomeRepository $incomeRepository,
        ExpenseRepository $expenseRepository
    ): Response
    {
        $user = $this->getUser();
        $now = new \DateTimeImmutable();
        $startOfWeek = $now->modify(('Monday' === $now->format('l')) ? 'this monday' : 'last monday')->setTime(0,0);
        $startOfMonth = $now->modify('first day of this month')->setTime(0,0);
        $startOfYear = $now->setDate($now->format('Y'), 1, 1)->setTime(0,0);

        // Semaine
        $weekIncomes = $incomeRepository->getTotalByUserAndPeriod($user, $startOfWeek, $now);
        $weekExpenses = $expenseRepository->getTotalByUserAndPeriod($user, $startOfWeek, $now);

        // Mois
        $monthIncomes = $incomeRepository->getTotalByUserAndPeriod($user, $startOfMonth, $now);
        $monthExpenses = $expenseRepository->getTotalByUserAndPeriod($user, $startOfMonth, $now);

        // Année
        $yearIncomes = $incomeRepository->getTotalByUserAndPeriod($user, $startOfYear, $now);
        $yearExpenses = $expenseRepository->getTotalByUserAndPeriod($user, $startOfYear, $now);

        // Tout
        $allIncomes = $incomeRepository->getTotalByUser($user);
        $allExpenses = $expenseRepository->getTotalByUser($user);

        $periodData = [
            'week'  => ['incomes' => $weekIncomes,  'expenses' => $weekExpenses],
            'month' => ['incomes' => $monthIncomes, 'expenses' => $monthExpenses],
            'year'  => ['incomes' => $yearIncomes,  'expenses' => $yearExpenses],
            'all'   => ['incomes' => $allIncomes,   'expenses' => $allExpenses],
        ];

        // Génération des labels et datasets pour le line chart
        $lineChartData = [
            'week' => [
                'labels' => [],
                'incomes' => [],
                'expenses' => [],
            ],
            'month' => [
                'labels' => [],
                'incomes' => [],
                'expenses' => [],
            ],
            'year' => [
                'labels' => [],
                'incomes' => [],
                'expenses' => [],
            ],
            'all' => [
                'labels' => [],
                'incomes' => [],
                'expenses' => [],
            ],
        ];

        // Semaine (7 derniers jours)
        for ($i = 6; $i >= 0; $i--) {
            $date = (new \DateTimeImmutable())->modify("-$i days");
            $label = $date->format('D d/m');
            $lineChartData['week']['labels'][] = $label;
            $lineChartData['week']['incomes'][] = $incomeRepository->getTotalByUserAndPeriod($user, $date->setTime(0,0), $date->setTime(23,59,59));
            $lineChartData['week']['expenses'][] = $expenseRepository->getTotalByUserAndPeriod($user, $date->setTime(0,0), $date->setTime(23,59,59));
        }

        // Mois (30 derniers jours)
        for ($i = 29; $i >= 0; $i--) {
            $date = (new \DateTimeImmutable())->modify("-$i days");
            $label = $date->format('d/m');
            $lineChartData['month']['labels'][] = $label;
            $lineChartData['month']['incomes'][] = $incomeRepository->getTotalByUserAndPeriod($user, $date->setTime(0,0), $date->setTime(23,59,59));
            $lineChartData['month']['expenses'][] = $expenseRepository->getTotalByUserAndPeriod($user, $date->setTime(0,0), $date->setTime(23,59,59));
        }

        // Année (par mois)
        for ($i = 0; $i < 12; $i++) {
            $date = (new \DateTimeImmutable('first day of January'))->modify("+$i months");
            $label = $date->format('M Y');
            $start = $date->setTime(0,0);
            $end = $date->modify('last day of this month')->setTime(23,59,59);
            $lineChartData['year']['labels'][] = $label;
            $lineChartData['year']['incomes'][] = $incomeRepository->getTotalByUserAndPeriod($user, $start, $end);
            $lineChartData['year']['expenses'][] = $expenseRepository->getTotalByUserAndPeriod($user, $start, $end);
        }

        // Tout (par année)
        $firstIncome = $incomeRepository->createQueryBuilder('i')
            ->select('MIN(i.incomeDate)')
            ->where('i.user = :user')
            ->setParameter('user', $user)
            ->getQuery()->getSingleScalarResult();
        $firstExpense = $expenseRepository->createQueryBuilder('e')
            ->select('MIN(e.expenseDate)')
            ->where('e.user = :user')
            ->setParameter('user', $user)
            ->getQuery()->getSingleScalarResult();

        $firstYear = min(
            $firstIncome ? (new \DateTimeImmutable($firstIncome))->format('Y') : date('Y'),
            $firstExpense ? (new \DateTimeImmutable($firstExpense))->format('Y') : date('Y')
        );
        $currentYear = (new \DateTimeImmutable())->format('Y');
        for ($y = $firstYear; $y <= $currentYear; $y++) {
            $label = (string)$y;
            $start = (new \DateTimeImmutable("$y-01-01"))->setTime(0,0);
            $end = (new \DateTimeImmutable("$y-12-31"))->setTime(23,59,59);
            $lineChartData['all']['labels'][] = $label;
            $lineChartData['all']['incomes'][] = $incomeRepository->getTotalByUserAndPeriod($user, $start, $end);
            $lineChartData['all']['expenses'][] = $expenseRepository->getTotalByUserAndPeriod($user, $start, $end);
        }

        return $this->render('home/index.html.twig', [
            'periodData' => $periodData,
            'lineChartData' => $lineChartData,
        ]);
    }
}


