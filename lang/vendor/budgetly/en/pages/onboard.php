
<?php

return [
    'wizard' => [
        'preparing_your_account' => [
            'title' => 'Preparing your account',
            'description' => 'Add your bank to start tracking your finances in one place.',
            'accounts' => 'Accounts',
        ],
        'income' => [
            'title' => 'Income',
            'description' => 'Add an income source to easily track your earnings and expenses! This helps you manage spending based on your current financial state.',
            'incomes' => 'Incomes',
            'helper' => [
                'name' => 'Example: Monthly Salary or Freelance earnings',
            ],
            'hint' => [
                'account' => 'Where does the income come from?',
            ],
        ],
        'expense' => [
            'title' => 'Expense',
            'description' => 'Add your expenses to start tracking and managing your spending—take control of your finances!',
            'expenses' => 'Expenses',
            'helper' => [
                'name' => 'Example: Home Rent or Transportation',
            ],
            'hint' => [
                'category' => 'What category does this expense belong to?',
            ],
        ],
    ],
];
