<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;
use App\Exceptions\ModelLockedException;
use TestMonitor\Lockable\Exceptions\ModelLockedException as BaseModelLockedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle base ModelLockedException by converting it to our custom one
        $this->renderable(function (BaseModelLockedException $e, Request $request) {
            if ($request->expectsJson()) {
                // Convert to our custom exception with a cleaner message
                $model = $e->getModel();
                $customException = new ModelLockedException();
                $customException->setModel($model);

                return response()->json([
                    'message' => $customException->getMessage(),
                    'success' => false
                ], 423); // 423 Locked
            }

            // For non-JSON requests, flash a warning message
            session()->flash('warning', (new ModelLockedException())->setModel($e->getModel())->getMessage());

            // Redirect back or to index
            return redirect()->back()->withInput();
        });

        // Handle our custom ModelLockedException
        $this->renderable(function (ModelLockedException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'success' => false
                ], 423); // 423 Locked
            }

            // For non-JSON requests, flash a warning message
            session()->flash('warning', $e->getMessage());

            // Redirect back or to index
            return redirect()->back()->withInput();
        });
    }
}
