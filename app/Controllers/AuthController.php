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
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role')
        ];

        $apiUrl = 'http://localhost:3000/api/auth/register';
        $response = api_request($apiUrl, 'POST', $data);

        if ($response && isset($response['token'])) {
            // Store token in session or cookie
            session()->set('auth_token', $response['token']);
            return redirect()->to('/register')->with('message', 'Registration Successful');
        } else {
            $errorMessage = $response['message'] ?? 'Registration Failed';
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }
    public function showLoginForm()
    {
        return view('auth/login');
    }
}
