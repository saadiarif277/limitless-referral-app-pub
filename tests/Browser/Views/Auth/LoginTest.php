<?php

namespace Tests\Browser\Views;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * Test if the default admin account can login.
     * 
     * @group Auth
     */
    public function testCanLoginAdmin(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::whereEmail('admin@'.$this->getDomain())->firstOrFail();
            $this->loginAndAssertDashboard($browser, $user);
        });
    }

    /**
     * Test if the default attorney account can login.
     * 
     * @group Auth
     */
    public function testCanLoginAttorney(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::whereEmail('attorney@'.$this->getDomain())->firstOrFail();
            $this->loginAndAssertDashboard($browser, $user);
        });
    }

    /**
     * Test if the default doctor account can login.
     * 
     * @group Auth
     */
    public function testCanLoginDoctor(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::whereEmail('doctor@'.$this->getDomain())->firstOrFail();
            $this->loginAndAssertDashboard($browser, $user);
        });
    }

    /**
     * Test if the default funding company account can login.
     * 
     * @group Auth
     */
    public function testCanLoginFundingCompany(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::whereEmail('funding.company@'.$this->getDomain())->firstOrFail();
            $this->loginAndAssertDashboard($browser, $user);
        });
    }

    /**
     * Test if the default office manager account can login.
     * 
     * @group Auth
     */
    public function testCanLoginOfficeManager(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::whereEmail('office.manager@'.$this->getDomain())->firstOrFail();
            $this->loginAndAssertDashboard($browser, $user);
        });
    }

    /**
     * Test if the default patient account can login.
     * 
     * @group Auth
     */
    public function testCanLoginPatient(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::whereEmail('patient@'.$this->getDomain())->firstOrFail();
            $this->loginAndAssertDashboard($browser, $user);
        });
    }

    /**
     * Get the domain.
     */
    public function getDomain()
    {
        return parse_url(config('app.url'), PHP_URL_HOST);
    }

    /**
     * Login using the given email and assert the user is redirected to the dashboard.
     */
    private function loginAndAssertDashboard(Browser $browser, User $user): void
    {
        $browser->visit('/login')
                ->assertGuest()
                ->assertSee('Welcome to the Referral System')
                ->assertSee('Please sign-in to your account and start the adventure.')
                ->typeSlowly('email', $user->email)
                ->typeSlowly('password', 'password')
                ->press('Login')
                ->waitForRoute('panel.dashboard')
                ->assertAuthenticatedAs($user)
                ->assertPathIs('/dashboard')
                ->assertSee('Welcome back')
                ->logout();
    }
}
