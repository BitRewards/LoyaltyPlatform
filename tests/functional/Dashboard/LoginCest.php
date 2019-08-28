<?php

namespace Dashboard;

use Page\Dashboard\LoginPage;
use Page\Dashboard\ToolsStatistic;

class LoginCest
{
    public function canLogin(\FunctionalTester $I, LoginPage $loginPage): void
    {
        $I->amOnPage($loginPage::URL);
        $I->seeElement('form');
        $I->submitForm('form', [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);
        $I->seeCurrentUrlEquals(ToolsStatistic::URL);
    }
}
