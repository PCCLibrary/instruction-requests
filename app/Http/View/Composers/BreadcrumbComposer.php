<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class BreadcrumbComposer
{
    public function compose(View $view)
    {
        $breadcrumbs = $this->generateBreadcrumbs();
        $view->with('breadcrumbs', $breadcrumbs);
    }

    protected function generateBreadcrumbs(): array
    {
        $route = Route::currentRouteName();
        $params = Route::current()->parameters();

        return match($route) {
            'dashboard.librarian' => [
                ['label' => 'Librarian Dashboard']
            ],
            'dashboard.scheduler' => [
                ['label' => 'Scheduler Dashboard']
            ],

            'statistics.index' => [
                ['label' => 'Statistics']
            ],
            'statistics.trends' => [
                ['label' => 'Statistics', 'route' => 'statistics.index'],
                ['label' => 'Trends Over Time']
            ],
            'statistics.detailed-export' => [
                ['label' => 'Statistics', 'route' => 'statistics.index'],
                ['label' => 'Detailed Export']
            ],
            'statistics.comparison' => [
                ['label' => 'Statistics', 'route' => 'statistics.index'],
                ['label' => 'Comparison Analysis']
            ],

            'campuses.index' => [
                ['label' => 'Campuses']
            ],
            'campuses.create' => [
                ['label' => 'Campuses', 'route' => 'campuses.index'],
                ['label' => 'New Campus']
            ],
            'campuses.edit' => [
                ['label' => 'Campuses', 'route' => 'campuses.index'],
                ['label' => 'Edit: ' . ($params['campus']->name ?? '')]
            ],
            'campuses.show' => [
                ['label' => 'Campuses', 'route' => 'campuses.index'],
                ['label' => $params['campus']->name ?? 'Campus']
            ],

            'instructors.index' => [
                ['label' => 'Instructors']
            ],
            'instructors.edit' => [
                ['label' => 'Instructors', 'route' => 'instructors.index'],
                ['label' => 'Edit: ' . ($params['instructor']->display_name ?? '')]
            ],
            'instructors.show' => [
                ['label' => 'Instructors', 'route' => 'instructors.index'],
                ['label' => $params['instructor']->display_name ?? 'Instructor']
            ],

            'users.index' => [
                ['label' => 'Librarians']
            ],
            'users.create' => [
                ['label' => 'Librarians', 'route' => 'users.index'],
                ['label' => 'New Librarian']
            ],
            'users.edit' => [
                ['label' => 'Librarians', 'route' => 'users.index'],
                ['label' => 'Edit: ' . ($params['user']->display_name ?? '')]
            ],
            'users.show' => [
                ['label' => 'Librarians', 'route' => 'users.index'],
                ['label' => $params['user']->display_name ?? 'Librarian']
            ],

            'instructionRequests.create' => [
                ['label' => $this->getDashboardLabel(), 'route' => $this->getDashboardRoute()],
                ['label' => 'New Request']
            ],
            'instructionRequests.edit' => [
                ['label' => $this->getDashboardLabel(), 'route' => $this->getDashboardRoute()],
                ['label' => 'Edit Request #' . ($params['instructionRequest'] ?? '')]
            ],
            'instructionRequests.show' => [
                ['label' => $this->getDashboardLabel(), 'route' => $this->getDashboardRoute()],
                ['label' => 'Request #' . ($params['instructionRequest'] ?? '')]
            ],

            'admin.index' => [
                ['label' => 'Admin Panel']
            ],

            'profile.index' => [
                ['label' => 'Profile']
            ],

            default => [
                ['label' => 'Dashboard', 'route' => 'dashboard']
            ]
        };
    }

    protected function getDashboardRoute(): string
    {
        return auth()->user()->is_scheduler
            ? 'dashboard.scheduler'
            : 'dashboard.librarian';
    }

    protected function getDashboardLabel(): string
    {
        return auth()->user()->is_scheduler
            ? 'Scheduler Dashboard'
            : 'Librarian Dashboard';
    }
}
