<?php
// app/Filters/AuthFilter.php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->has('user')) {
            return redirect()->to('/login')
                ->with('error', 'Please log in');
        }

        // If role is specified in arguments, check user's role
        if ($arguments && count($arguments) > 0) {
            $requiredRole = $arguments[0];
            $userRole = session()->get('user')['role'];

            if ($userRole !== $requiredRole) {
                return redirect()
                    ->to('/dashboard')
                    ->with('error', "You don't have permission to access this page");
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after execution
    }
}
