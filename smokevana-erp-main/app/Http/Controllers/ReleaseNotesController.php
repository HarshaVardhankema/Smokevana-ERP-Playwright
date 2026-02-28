<?php

namespace App\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Module;

class ReleaseNotesController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display the release notes page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all installed modules
        $modules = Module::toCollection()->toArray();
        $installed_modules = [];
        
        foreach ($modules as $module => $details) {
            if ($this->moduleUtil->isModuleInstalled($details['name'])) {
                $module_info = [
                    'name' => $details['name'],
                    'description' => $details['description'] ?? '',
                    'version' => null
                ];
                
                // Get version information if available
                if ($this->moduleUtil->isModuleInstalled($details['name'])) {
                    $version_info = $this->moduleUtil->getModuleVersionInfo($details['name']);
                    if (!empty($version_info)) {
                        $module_info['version'] = $version_info['installed_version'] ?? '1.0';
                    } else {
                        $module_info['version'] = '1.0';
                    }
                }
                
                $installed_modules[] = $module_info;
            }
        }

        // Get enabled modules for the business
        $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
        
        return view('release_notes.index', compact('installed_modules', 'enabled_modules'));
    }
}

