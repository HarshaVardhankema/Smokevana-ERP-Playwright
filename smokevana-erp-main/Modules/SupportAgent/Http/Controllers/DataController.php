<?php

namespace Modules\SupportAgent\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'support_agent.access',
                'label' => __('supportagent::lang.access_support_agent'),
                'default' => true, // Enable by default for all users
            ],
        ];
    }

    /**
     * Adds Support Agent menu to admin sidebar
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

        // Only show AI Support when enabled in Business Settings (Modules tab)
        if (auth()->check() && in_array('supportagent', $enabled_modules)) {
            Menu::modify('admin-sidebar-menu', function ($menu) {
                $menu->url(
                    action([\Modules\SupportAgent\Http\Controllers\SupportAgentController::class, 'index']),
                    __('supportagent::lang.menu_support_agent'),
                    [
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-robot">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M6 4m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
                            <path d="M12 2v2" />
                            <path d="M9 12v9" />
                            <path d="M15 12v9" />
                            <path d="M5 16l4 -2" />
                            <path d="M15 14l4 2" />
                            <path d="M9 18h6" />
                            <path d="M10 8v.01" />
                            <path d="M14 8v.01" />
                        </svg>',
                        'active' => request()->segment(1) == 'support-agent'
                    ]
                )->order(96); // Place near the bottom of the menu
            });
        }
    }

    /**
     * Returns module-specific superadmin package permissions
     *
     * @return array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'supportagent_module',
                'label' => __('supportagent::lang.support_agent'),
                'default' => true,
            ],
        ];
    }
}
