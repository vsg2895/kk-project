<?php namespace Api\Http\Controllers;

use Shared\Http\Controllers\Controller;

/**
 * Class ApiController
 * @package Api\Http\Controllers
 */
class ApiController extends Controller
{
    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = ['success' => true])
    {
        return response()->json($data);
    }

    /**
     * @param array $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($data = ['success' => false], $status = 400)
    {
        return response()->json($data, $status);
    }
}
