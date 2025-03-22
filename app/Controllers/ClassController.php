<?php
// app/Controllers/ClassController.php
namespace App\Controllers;

use CodeIgniter\Controller;

class ClassController extends Controller
{
    protected $helpers = ['api', 'form'];

    // Show dashboard based on user role
    public function dashboard()
    {
        $userRole = session()->get('user')['role'];
        $apiUrl = 'http://localhost:3000/api/class/my-classes';

        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'GET', [], $headers);

        if (!$response || isset($response['error'])) {
            return view('dashboard/main', [
                'classes' => [],
                'error' => $response['message'] ?? 'Failed to fetch classes'
            ]);
        }

        return view('dashboard/main', [
            'classes' => $response['classes'] ?? [],
            'role' => $userRole
        ]);
    }

    // Show create class form (only for teachers)
    public function createForm()
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to('/dashboard')
                ->with('error', 'Only teachers can create classes');
        }

        return view('class/create');
    }

    // Process class creation
    public function create()
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to('/dashboard')
                ->with('error', 'Only teachers can create classes');
        }

        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required|min_length[3]',
            'description' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        $apiUrl = 'http://localhost:3000/api/class/create';
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'POST', $data, $headers);

        if ($response && isset($response['classId'])) {
            return redirect()->to('/dashboard')
                ->with('success', 'Class created successfully! Class code: ' . $response['code']);
        } else {
            $errorMessage = $response['message'] ?? 'Failed to create class. Please try again.';
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    // Show join class form (for both students and teachers)
    public function joinForm()
    {
        $userRole = session()->get('user')['role'];
        $viewData = [
            'role' => $userRole
        ];

        return view('class/join', $viewData);
    }

    // Process class join
    public function join()
    {
        $userRole = session()->get('user')['role'];
        $validation = \Config\Services::validation();

        $rules = [
            'code' => 'required|min_length[6]|max_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid class code format');
        }

        $data = [
            'code' => $this->request->getPost('code')
        ];

        $apiUrl = 'http://localhost:3000/api/class/join';
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'POST', $data, $headers);

        if ($response && !isset($response['error'])) {
            $successMessage = $userRole === 'teacher'
                ? 'Successfully joined as a contributor to the class: ' . ($response['class']['name'] ?? '')
                : 'Successfully joined the class: ' . ($response['class']['name'] ?? '');

            return redirect()->to('/dashboard')
                ->with('success', $successMessage);
        } else {
            $errorMessage = $response['message'] ?? 'Failed to join class. Please try again.';
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    // Class details
    public function details($id)
    {
        $apiUrl = "http://localhost:3000/api/class/{$id}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'GET', [], $headers);

        if (!$response || isset($response['error'])) {
            return redirect()->to('/dashboard')
                ->with('error', $response['message'] ?? 'Failed to fetch class details');
        }

        return view('class/details', [
            'class' => $response['class'] ?? [],
            'students' => $response['students'] ?? [],
            'contributors' => $response['contributors'] ?? [],
            'role' => session()->get('user')['role'],
            'userClassRole' => $response['class']['userRole'] ?? 'student' // Can be 'owner', 'contributor', or 'student'
        ]);
    }
}
