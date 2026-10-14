<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    /**
     * System configuration
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return   mixed
     */
    public function protection(Request $request)
    {
        EnvSet::setKey(strtoupper('app_pack'), $request->package_hash ?? null);
        EnvSet::setKey(strtoupper('app_debug'), $request->app_debug ?? false);
        EnvSet::save();
        return redirect('/?version=' . config('app.version') . '&message=Application_configuration_saved_successfully');
    }

    /**
     * Provides default settings for all controllers
     * extended by controller
     *
     * @return object
     */
    protected function master(): object
    {
        return Setting::find(1);
    }

    /**
     * DataTable sorting for common resources
     *
     * @param mixed $request
     *
     * @return array
     */
    protected function sort($request): array
    {
        return $request->get('sort', json_decode(
            json_encode(['order' => 'asc', 'column' => 'created_at']),
            true,
            512,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * Generate pagination for common dataTables
     * @param \Illuminate\Database\Eloquent\Collection $items
     *
     * @return array
     */
    protected function pagination($items): array
    {
        return [
            'currentPage' => $items->currentPage(),
            'perPage' => $items->perPage(),
            'total' => $items->total(),
            'totalPages' => $items->lastPage(),
        ];
    }

    protected function getAdmin($userId = 1)
    {
        $admin = User::find($userId);

        if (null === $admin) {
            // Handle the case when the user is not found
            return null;
        }

        // Check if the email is valid and not equal to "admin@admin.com"
        $adminEmail = $admin->email;

        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL) || 'admin@admin.com' === $adminEmail || 'test@test.com' === $adminEmail) {
            // Return null if the email is invalid or equal to "admin@admin.com"
            return null;
        }

        return $admin;
    }
}
