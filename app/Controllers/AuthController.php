<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $helpers = ['api', 'form'];

    public function showRegistrationForm()
    {
        return view('auth/register');
    }

    public function register()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[teacher,student]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role')
        ];

        $apiUrl = 'http://localhost:3000/api/auth/register';
        $response = api_request($apiUrl, 'POST', $data);

        if ($response && isset($response['success'])) {
            return redirect()->to('/login')
                ->with('success', 'Registration successful! Please login to continue.');
        } else {
            $errorMessage = $response['message'] ?? 'Registration failed. Please try again.';
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }
    public function showLoginForm()
    {
        return view('auth/login');
    }
    public function login()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password format');
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        $apiUrl = 'http://localhost:3000/api/auth/login';
        $response = api_request($apiUrl, 'POST', $data);

        if ($response && isset($response['token'])) {
            session()->set([
                'auth_token' => $response['token'],
                'user' => $response['user'] ?? ['name' => $response['name'] ?? 'User'],
                'role' => $response['user']['role'] ?? 'user'
            ]);

            return redirect()->to('/dashboard')
                ->with('success', 'Selamat datang, ' . ($response['user']['name'] ?? 'User'));
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Invalid credentials');
        }
    }


    // Add logout method
    public function logout()
    {
        session()->remove(['auth_token', 'user']);
        return redirect()->to('/login')->with('message', 'Logged out successfully');
    }
}
