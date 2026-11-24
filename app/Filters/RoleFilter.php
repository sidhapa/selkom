<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Jika ada argument role
        if ($arguments) {
            $userRole = session()->get('role');
            if (!in_array($userRole, $arguments)) {
                // Langsung return response 403 Forbidden
                return service('response')
                    ->setStatusCode(403)
                    ->setBody('403 Forbidden: Anda tidak memiliki hak akses ke halaman ini.')
                    ->send();
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
