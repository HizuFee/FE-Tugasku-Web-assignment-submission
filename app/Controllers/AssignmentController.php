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
            'class' => $response['class'] ?? [],
            'students' => $response['students'] ?? []
        ]);
    }

    // Process assignment creation
    public function create($classId)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return redirect()->to("/class/details/{$classId}")
                ->with('error', 'Only teachers can create assignments');
        }

        // Debug: Log all POST data
        log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
        log_message('debug', 'FILES data: ' . print_r($this->request->getFiles(), true));

        $validation = \Config\Services::validation();
        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'required',
            'deadline' => 'required',
            'file' => 'uploaded[file]|max_size[file,10240]|ext_in[file,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,rtf,odt,jpg,jpeg,png,gif,bmp,zip,rar,tar,7z,csv]',
            'selected_students' => 'required'
        ];

        // Make file upload optional
        if (!$this->request->getFile('file')->isValid()) {
            unset($rules['file']);
        }

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation errors: ' . print_r($validation->getErrors(), true));
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }

        // Get selected students
        $selectedStudents = $this->request->getPost('selected_students');
        log_message('debug', 'Selected students (raw): ' . print_r($selectedStudents, true));

        // Ensure selected_students is an array and not empty
        if (empty($selectedStudents) || (!is_array($selectedStudents) && !is_string($selectedStudents))) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one student');
        }

        // Convert to array if string
        if (!is_array($selectedStudents)) {
            $selectedStudents = [$selectedStudents];
        }

        // Remove any empty values
        $selectedStudents = array_filter($selectedStudents, function ($value) {
            return !empty(trim($value));
        });

        if (empty($selectedStudents)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one student');
        }

        // Debug: Log processed selected students
        log_message('debug', 'Selected students (processed): ' . print_r($selectedStudents, true));

        $data = [
            'class_id' => $classId,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'deadline' => $this->request->getPost('deadline'),
            'selected_students' => $selectedStudents
        ];

        // Debug: Log final data
        log_message('debug', 'Final data to be sent: ' . print_r($data, true));

        // Handle file upload
        $file = $this->request->getFile('file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Use cURL to handle file upload to API
            $apiUrl = 'http://localhost:3000/api/assignment/create';
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            // Create a CURLFile object
            $tmpName = $file->getTempName();
            $name = $file->getName();

            $curl = curl_init($apiUrl);
            $postData = [
                'class_id' => $data['class_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'deadline' => $data['deadline'],
                'file' => curl_file_create($tmpName, $file->getClientMimeType(), $name)
            ];

            // Add selected students to postData
            foreach ($selectedStudents as $studentId) {
                $postData['selected_students[]'] = $studentId;
            }

            // Debug: Log curl data
            log_message('debug', 'CURL post data: ' . print_r($postData, true));

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'x-auth-token: ' . session()->get('auth_token')
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (curl_errno($curl)) {
                log_message('error', 'Curl error: ' . curl_error($curl));
            }

            // Debug: Log response
            log_message('debug', 'API Response: ' . print_r($response, true));
            log_message('debug', 'HTTP Code: ' . $httpCode);

            curl_close($curl);

            if ($httpCode === 201) {
                $response = json_decode($response, true);
                return redirect()->to("/class/{$classId}/assignments")
                    ->with('success', 'Assignment created successfully!');
            } else {
                $responseData = json_decode($response, true);
                $errorMessage = $responseData['message'] ?? 'Failed to create assignment. Please try again.';
                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }
        } else {
            // No file uploaded, use regular API request
            $apiUrl = 'http://localhost:3000/api/assignment/create';
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            // Debug: Log API request without file
            log_message('debug', 'API request data (no file): ' . print_r($data, true));

            $response = api_request($apiUrl, 'POST', $data, $headers);

            // Debug: Log API response
            log_message('debug', 'API Response (no file): ' . print_r($response, true));

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

        // Prepare file URL with token
        $fileUrl = null;
        if (!empty($response['assignment']['file_path'])) {
            $token = session()->get('auth_token');
            $fileUrl = "http://localhost:3000/api/assignment/{$assignmentId}/download?token=" . $token;
        }

        return view('assignment/details', [
            'assignment' => $response['assignment'] ?? [],
            'userRole' => $userRole,
            'userClassRole' => $response['userClassRole'] ?? 'student',
            'submissions' => $response['submissions'] ?? [],
            'userSubmission' => $response['userSubmission'] ?? null,
            'classId' => $classId,
            'fileUrl' => $fileUrl
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

        // Get class details to get list of students
        $classApiUrl = "http://localhost:3000/api/class/{$classId}";
        $classResponse = api_request($classApiUrl, 'GET', [], $headers);

        if (!$classResponse || isset($classResponse['error'])) {
            return redirect()->to("/class/{$classId}/assignments")
                ->with('error', $classResponse['message'] ?? 'Failed to fetch class details');
        }

        return view('assignment/edit', [
            'assignment' => $response['assignment'] ?? [],
            'students' => $classResponse['students'] ?? [],
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
            'deadline' => 'required',
            'file' => 'max_size[file,10240]|ext_in[file,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,rtf,odt,jpg,jpeg,png,gif,bmp,zip,rar,tar,7z,csv]',
            'selected_students' => 'required'
        ];

        // Check if file exists and is valid
        if (!$this->request->getFile('file')->isValid()) {
            unset($rules['file']);
        }

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation errors: ' . print_r($validation->getErrors(), true));
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validation->getErrors()));
        }

        // Get selected students
        $selectedStudents = $this->request->getPost('selected_students');
        log_message('debug', 'Selected students (raw): ' . print_r($selectedStudents, true));

        // Ensure selected_students is an array and not empty
        if (empty($selectedStudents)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one student');
        }

        // Convert comma-separated string to array
        if (is_string($selectedStudents)) {
            $selectedStudents = explode(',', $selectedStudents);
        }

        // Remove any empty values
        $selectedStudents = array_filter($selectedStudents, function ($value) {
            return !empty(trim($value));
        });

        if (empty($selectedStudents)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one student');
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'deadline' => $this->request->getPost('deadline'),
            'selected_students' => $selectedStudents
        ];

        // Handle file upload
        $file = $this->request->getFile('file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Use cURL to handle file upload to API
            $apiUrl = "http://localhost:3000/api/assignment/{$assignmentId}";
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            // Create a CURLFile object
            $tmpName = $file->getTempName();
            $name = $file->getName();

            $curl = curl_init($apiUrl);
            $postData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'deadline' => $data['deadline'],
                'file' => curl_file_create($tmpName, $file->getClientMimeType(), $name)
            ];

            // Add selected students to postData
            foreach ($selectedStudents as $studentId) {
                $postData['selected_students[]'] = $studentId;
            }

            // Debug: Log curl data
            log_message('debug', 'CURL post data: ' . print_r($postData, true));

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'x-auth-token: ' . session()->get('auth_token')
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (curl_errno($curl)) {
                log_message('error', 'Curl error: ' . curl_error($curl));
            }

            // Debug: Log response
            log_message('debug', 'API Response: ' . print_r($response, true));
            log_message('debug', 'HTTP Code: ' . $httpCode);

            curl_close($curl);

            if ($httpCode === 200) {
                $response = json_decode($response, true);
                return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                    ->with('success', 'Assignment updated successfully');
            } else {
                $responseData = json_decode($response, true);
                $errorMessage = $responseData['message'] ?? 'Failed to update assignment. Please try again.';
                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }
        } else {
            // No file uploaded, use regular API request
            $apiUrl = "http://localhost:3000/api/assignment/{$assignmentId}";
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            // Debug: Log API request without file
            log_message('debug', 'API request data (no file): ' . print_r($data, true));

            $response = api_request($apiUrl, 'PUT', $data, $headers);

            // Debug: Log API response
            log_message('debug', 'API Response (no file): ' . print_r($response, true));

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

    // Download assignment file
    public function downloadFile($classId, $assignmentId)
    {
        $token = session()->get('auth_token');
        if (!$token) {
            return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                ->with('error', 'Authentication required');
        }

        // Pastikan parameter download=true
        $downloadUrl = "http://localhost:3000/api/assignment/{$assignmentId}/download?token={$token}&download=true";

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->request('HEAD', $downloadUrl, [
                'http_errors' => false,
                'timeout' => 10
            ]);

            if ($response->getStatusCode() === 200) {
                return redirect()->to($downloadUrl);
            } else {
                return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                    ->with('error', 'Could not download file. Server responded with: ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                ->with('error', 'Connection error: ' . $e->getMessage());
        }
    }

    // Get file content for preview
    public function previewFile($classId, $assignmentId)
    {
        try {
            $token = session()->get('auth_token');
            if (!$token) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Token tidak ditemukan'
                ]);
            }

            $client = \Config\Services::curlrequest();
            $response = $client->get("http://localhost:3000/api/assignment/{$assignmentId}/download", [
                'headers' => [
                    'x-auth-token' => $token
                ],
                'query' => [
                    'download' => 'false',
                    'forcePreview' => 'true'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mengambil file'
                ]);
            }

            $contentType = $response->getHeaderLine('Content-Type');
            if (empty($contentType)) {
                $contentType = 'application/octet-stream';
            }

            $fileContent = $response->getBody();

            return $this->response->setJSON([
                'status' => 'success',
                'contentType' => $contentType,
                'fileContent' => base64_encode($fileContent),
                'fileUrl' => "http://localhost:3000/api/assignment/{$assignmentId}/download?token={$token}"
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
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
            'notes' => 'permit_empty',
            'file' => 'max_size[file,10240]|ext_in[file,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,rtf,odt,jpg,jpeg,png,gif,bmp,zip,rar,tar,7z,csv]',
        ];

        // Check if file exists and is valid
        if (!$this->request->getFile('file')->isValid()) {
            unset($rules['file']);
        }

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

    public function downloadSubmissionFile($classId, $assignmentId, $submissionId)
    {
        try {
            $token = session()->get('auth_token');
            if (!$token) {
                return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                    ->with('error', 'Token tidak ditemukan');
            }

            $client = \Config\Services::curlrequest();
            $response = $client->get("http://localhost:3000/api/assignment/{$assignmentId}/submissions/{$submissionId}/download", [
                'headers' => [
                    'x-auth-token' => $token
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                    ->with('error', 'Gagal mengunduh file');
            }

            // Get filename from Content-Disposition header
            $contentDisposition = $response->getHeaderLine('Content-Disposition');
            preg_match('/filename="([^"]+)"/', $contentDisposition, $matches);
            $filename = $matches[1] ?? 'submission_file';

            // Set headers for download
            $this->response->setHeader('Content-Type', 'application/octet-stream');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

            return $this->response->setBody($response->getBody());
        } catch (\Exception $e) {
            return redirect()->to("/class/{$classId}/assignments/{$assignmentId}")
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function previewSubmissionFile($classId, $assignmentId, $submissionId)
    {
        try {
            $token = session()->get('auth_token');
            if (!$token) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Token tidak ditemukan'
                ]);
            }

            $client = \Config\Services::curlrequest();
            $response = $client->get("http://localhost:3000/api/assignment/{$assignmentId}/submissions/{$submissionId}/preview", [
                'headers' => [
                    'x-auth-token' => $token
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mengambil file'
                ]);
            }

            $contentType = $response->getHeaderLine('Content-Type');
            if (empty($contentType)) {
                $contentType = 'application/octet-stream';
            }

            $fileContent = $response->getBody();

            return $this->response->setJSON([
                'status' => 'success',
                'contentType' => $contentType,
                'fileContent' => base64_encode($fileContent),
                'fileUrl' => "http://localhost:3000/api/assignment/{$assignmentId}/submissions/{$submissionId}/download?token={$token}"
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
