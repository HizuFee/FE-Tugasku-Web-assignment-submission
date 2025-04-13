<?php
// app/Controllers/TopicController.php
namespace App\Controllers;

use CodeIgniter\Controller;

class TopicController extends Controller
{
    protected $helpers = ['api', 'form'];

    public function create()
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya guru yang dapat membuat topik'
            ]);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required|min_length[3]',
            'class_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'class_id' => $this->request->getPost('class_id')
        ];

        $apiUrl = 'http://localhost:3000/api/topic/create';
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'POST', $data, $headers);

        return $this->response->setJSON($response);
    }

    public function update($id)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya guru yang dapat mengubah topik'
            ]);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        $apiUrl = "http://localhost:3000/api/topic/{$id}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'PUT', $data, $headers);

        return $this->response->setJSON($response);
    }

    public function delete($id)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya guru yang dapat menghapus topik'
            ]);
        }

        $apiUrl = "http://localhost:3000/api/topic/{$id}";
        $headers = [
            'x-auth-token' => session()->get('auth_token')
        ];

        $response = api_request($apiUrl, 'DELETE', [], $headers);

        return $this->response->setJSON($response);
    }

    public function getClassTopics($classId)
    {
        try {
            $apiUrl = "http://localhost:3000/api/topic/class/{$classId}";
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            $response = api_request($apiUrl, 'GET', [], $headers);

            if (!$response) {
                throw new \Exception('Failed to fetch topics');
            }

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error in getClassTopics: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data topik'
            ]);
        }
    }

    public function assignToAssignment($assignmentId)
    {
        if (session()->get('user')['role'] !== 'teacher') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya guru yang dapat mengatur topik tugas'
            ]);
        }

        try {
            $topicIds = $this->request->getPost('topic_ids');
            if (is_string($topicIds)) {
                $topicIds = explode(',', $topicIds);
            }

            $data = [
                'topic_ids' => $topicIds
            ];

            $apiUrl = "http://localhost:3000/api/topic/assignment/{$assignmentId}";
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            $response = api_request($apiUrl, 'POST', $data, $headers);

            if (!$response) {
                throw new \Exception('Failed to assign topics');
            }

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error in assignToAssignment: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menetapkan topik ke tugas'
            ]);
        }
    }

    public function getAssignmentTopics($assignmentId)
    {
        try {
            $apiUrl = "http://localhost:3000/api/topic/assignment/{$assignmentId}";
            $headers = [
                'x-auth-token' => session()->get('auth_token')
            ];

            $response = api_request($apiUrl, 'GET', [], $headers);

            if (!$response) {
                throw new \Exception('Failed to fetch assignment topics');
            }

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error in getAssignmentTopics: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data topik tugas'
            ]);
        }
    }
}
