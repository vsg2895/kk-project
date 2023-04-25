<?php namespace Student\Http\Controllers;

use Shared\Http\Controllers\Controller;

/**
 * Class DashboardController
 * @package Student\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dashboard()
    {
        return redirect()->route('student::bookings.index');
    }
}
