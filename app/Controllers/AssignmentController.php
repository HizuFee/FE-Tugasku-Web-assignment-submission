<?php
// app/Controllers/AssignmentController.php
namespace App\Controllers;

use CodeIgniter\Controller;

class AssignmentController extends Controller
{
    protected $helpers = ['api', 'form'];

    // List all assignments for a class
    public function listAssignments($classId)
    {
        $apiUrl = "http://localhost:3000/api/assignment/class/{$classId}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'GET', [], $headers);

        if (!$response || isset($response['error'])) {
            return redirect()->to("/class/details/{$classId}")
                ->with('error', $response['message'] ?? 'Failed to fetch assignments');
        }

        // Get class details
        $classApiUrl = "http://localhost:3000/api/class/{$classId}";
        $classResponse = api_request($classApiUrl, 'GET', [], $headers);

        if (!$classResponse || isset($classResponse['error'])) {
            return redirect()->to('/dashboard')
                ->with('error', $classResponse['message'] ?? 'Failed to fetch class details');
        }

        return view('assignment/list', [
            'assignments' => $response['assignments'] ?? [],
            'class' => $classResponse['class'] ?? [],
            'userRole' => session()->get('user')['role'],
            'userClassRole' => $classResponse['class']['userRole'] ?? 'student'
        ]);
    }

    // Show assignment creation form
    public function createForm($classId)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to("/class/details/{$classId}")
                ->with('error', 'Only teachers can create assignments');
        }

        // Get class details to verify access
        $apiUrl = "http://localhost:3000/api/class/{$classId}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'GET', [], $headers);

        if (!$response || isset($response['error'])) {
            return redirect()->to('/dashboard')
                ->with('error', $response['message'] ?? 'Failed to fetch class details');
        }

        // Check if user is owner or contributor
        if (!in_array($response['class']['userRole'], ['owner', 'contributor'])) {
            return redirect()->to("/class/details/{$classId}")
                ->with('error', 'You do not have permission to create assignments for this class');
        }

        return view('assignment/create', [
            'class' => $response['class'] ?? []
        ]);
    }

    // Process assignment creation
    public function create($classId)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to("/class/details/{$classId}")
                ->with('error', 'Only teachers can create assignments');
        }

        $validation = \Config\Services::validation();
        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'required',
            'deadline' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }

        $data = [
            'class_id' => $classId,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'deadline' => $this->request->getPost('deadline')
        ];

        $apiUrl = 'http://localhost:3000/api/assignment/create';
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'POST', $data, $headers);

        if ($response && isset($response['assignment'])) {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('success', 'Assignment created successfully!');
        } else {
            $errorMessage = $response['message'] ?? 'Failed to create assignment. Please try again.';
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    // View assignment details
    public function details($classId, $assignmentId)
    {
        $apiUrl = "http://localhost:3000/api/assignment/{$assignmentId}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'GET', [], $headers);

        if (!$response || isset($response['error'])) {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', $response['message'] ?? 'Failed to fetch assignment details');
        }

        $userRole = session()->get('user')['role'];

        return view('assignment/details', [
            'assignment' => $response['assignment'] ?? [],
            'userRole' => $userRole,
            'userClassRole' => $response['userClassRole'] ?? 'student',
            'submissions' => $response['submissions'] ?? [],
            'userSubmission' => $response['userSubmission'] ?? null,
            'classId' => $classId
        ]);
    }

    // Show assignment edit form
    public function editForm($classId, $assignmentId)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', 'Only teachers can edit assignments');
        }

        $apiUrl = "http://localhost:3000/api/assignment/{$assignmentId}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'GET', [], $headers);

        if (!$response || isset($response['error'])) {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', $response['message'] ?? 'Failed to fetch assignment details');
        }

        // Check if user is owner or contributor
        if (!in_array($response['userClassRole'], ['owner', 'contributor'])) {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', 'You do not have permission to edit this assignment');
        }

        return view('assignment/edit', [
            'assignment' => $response['assignment'] ?? [],
            'classId' => $classId
        ]);
    }

    // Process assignment update
    public function update($classId, $assignmentId)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', 'Only teachers can update assignments');
        }

        $validation = \Config\Services::validation();
        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'required',
            'deadline' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'deadline' => $this->request->getPost('deadline')
        ];

        $apiUrl = "http://localhost:3000/api/assignment/{$assignmentId}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'PUT', $data, $headers);

        if ($response && !isset($response['error'])) {
            return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                ->with('success', 'Assignment updated successfully');
        } else {
            $errorMessage = $response['message'] ?? 'Failed to update assignment. Please try again.';
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    // Delete assignment
    public function delete($classId, $assignmentId)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', 'Only teachers can delete assignments');
        }

        $apiUrl = "http://localhost:3000/api/assignment/{$assignmentId}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'DELETE', [], $headers);

        if ($response && !isset($response['error'])) {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('success', 'Assignment deleted successfully');
        } else {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', $response['message'] ?? 'Failed to delete assignment');
        }
    }

    // Submit assignment (for students)
    public function submit($classId, $assignmentId)
    {
        if (session()->get('user')['role'] !== 'student') {
            return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                ->with('error', 'Only students can submit assignments');
        }

        $validation = \Config\Services::validation();
        $rules = [
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }

        $notes = $this->request->getPost('notes');

        // Handle file upload
        $file = $this->request->getFile('file');
        $fileName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Prepare data for API
            $tmpName = $file->getTempName();
            $name = $file->getName();

            $apiUrl = "http://localhost:3000/api/assignment/{$assignmentId}/submit";
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            // Create multipart form data
            $data = [
                'notes' => $notes
            ];

            // Use cURL to handle file upload to API
            $curl = curl_init($apiUrl);

            // Create a CURLFile object
            $cfile = curl_file_create($tmpName, $file->getClientMimeType(), $name);
            $postData = [
                'file' => $cfile,
                'notes' => $notes
            ];

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'x-auth-token: ' . session()->get('auth_token')
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpCode === 200) {
                $response = json_decode($response, true);

                if ($response && isset($response['submission'])) {
                    return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                        ->with('success', 'Assignment submitted successfully' .
                            ($response['isLate'] ? ' (Note: Submission was after the deadline)' : ''));
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit assignment. Please try again.');
        } else if (!$file || !$file->isValid()) {
            // Submit without file
            $apiUrl = "http://localhost:3000/api/assignment/{$assignmentId}/submit";
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            $data = [
                'notes' => $notes
            ];

            $response = api_request($apiUrl, 'POST', $data, $headers);

            if ($response && isset($response['submission'])) {
                return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                    ->with('success', 'Assignment submitted successfully' .
                        ($response['isLate'] ? ' (Note: Submission was after the deadline)' : ''));
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $response['message'] ?? 'Failed to submit assignment. Please try again.');
            }
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to upload file. Please try again.');
        }
    }

    // Grade submission (for teachers)
    public function gradeSubmission($classId, $submissionId)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', 'Only teachers can grade submissions');
        }

        $validation = \Config\Services::validation();
        $rules = [
            'grade' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'feedback' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }

        $data = [
            'grade' => $this->request->getPost('grade'),
            'feedback' => $this->request->getPost('feedback')
        ];

        $apiUrl = "http://localhost:3000/api/assignment/submission/{$submissionId}/grade";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'PUT', $data, $headers);

        if ($response && !isset($response['error'])) {
            return redirect()->back()
                ->with('success', 'Submission graded successfully');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Failed to grade submission. Please try again.');
        }
    }
}
