<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\CSRF;
use Core\Helper;
use Core\Session;

class AuthController extends Controller {

    public function showLogin(): void {
        if (Auth::check()) {
            Helper::redirect('dashboard');
        }
        $this->renderAuthView('auth/login');
    }

    public function login(): void {
        $this->validateCsrf();

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            Session::setFlash('error', 'Please enter both username and password.');
            Helper::redirect('login');
        }

        if (Auth::attempt($username, $password)) {
            Session::setFlash('success', 'Welcome to Agri Co-Op ERP!');
            Helper::redirect('dashboard');
        } else {
            Session::setFlash('error', 'Invalid username or password.');
            Helper::redirect('login');
        }
    }

    public function logout(): void {
        Auth::logout();
        Session::setFlash('info', 'You have been logged out successfully.');
        Helper::redirect('login');
    }
}
