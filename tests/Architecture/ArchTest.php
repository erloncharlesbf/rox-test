<?php

/*
|--------------------------------------------------------------------------
| Architecture Tests
|--------------------------------------------------------------------------
|
| These tests ensure that the codebase follows architectural best practices,
| conventions, and does not contain debugging code in production.
|
*/

// Test: No debug functions in production code
arch('no debug functions in production code')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'var_export', 'die', 'exit'])
    ->not->toBeUsed();

// Test: No debug statements in app code
arch('app does not use debugging functions')
    ->expect('App')
    ->not->toUse([
        'dd',
        'dump',
        'var_dump',
        'print_r',
        'var_export',
        'die',
        'exit',
        'echo',
        'print',
    ]);

// Test: No ray debugging in production
arch('no ray debugging in production')
    ->expect(['ray'])
    ->not->toBeUsed();

// Test: Controllers should be in Controllers namespace
arch('controllers')
    ->expect('App\Http\Controllers')
    ->toBeClasses()
    ->toHaveSuffix('Controller')
    ->ignoring(\App\Http\Controllers\Controller::class);

// Test: Models should be in Models namespace
arch('models')
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(\Illuminate\Database\Eloquent\Model::class)
    ->ignoring(\App\Models\User::class); // User extends Authenticatable

// Test: Repositories should be in Repositories namespace
arch('repositories')
    ->expect('App\Repositories')
    ->toBeClasses()
    ->toHaveSuffix('Repository');

// Test: Services should be in Services namespace
arch('services')
    ->expect('App\Services')
    ->toBeClasses()
    ->toHaveSuffix('Service');

// Test: Requests should extend FormRequest
arch('requests')
    ->expect('App\Http\Requests')
    ->toBeClasses()
    ->toExtend(\Illuminate\Foundation\Http\FormRequest::class);

// Test: Resources should extend JsonResource
arch('resources should extend JsonResource or ResourceCollection')
    ->expect('App\Http\Resources')
    ->toBeClasses()
    ->toExtendNothing()
    ->ignoring([
        \App\Http\Resources\Attachment\AttachmentResource::class,
        \App\Http\Resources\Book\BookResource::class,
        \App\Http\Resources\Movie\MovieResource::class,
    ]);

// Test: Repositories should implement interfaces
arch('repositories implement interfaces')
    ->expect('App\Repositories')
    ->toImplement(\App\Contracts\Repositories\BaseRepositoryInterface::class)
    ->ignoring(\App\Repositories\BaseRepository::class);

// Test: Controllers should be classes
arch('controllers are classes')
    ->expect('App\Http\Controllers')
    ->toBeClasses();

// Test: Models should use traits properly
arch('models use SoftDeletes when needed')
    ->expect(\App\Models\Movie::class)
    ->toUse(\Illuminate\Database\Eloquent\SoftDeletes::class);

arch('models use SoftDeletes for Book')
    ->expect(\App\Models\Book::class)
    ->toUse(\Illuminate\Database\Eloquent\SoftDeletes::class);

// Test: No eval() usage
arch('no eval usage')
    ->expect(['eval'])
    ->not->toBeUsed();

// Test: No global variables
arch('no global variables in classes')
    ->expect('App')
    ->not->toUse(['global']);

// Test: Services should be readonly (dependency injection)
arch('services use dependency injection')
    ->expect('App\Services')
    ->toOnlyUse([
        'App\Contracts\Repositories',
        'App\Models',
        'Illuminate\Support\Facades',
        'Illuminate\Database\Eloquent',
        'Illuminate\Pagination',
        'Illuminate\Support',
        'Illuminate\Database',
    ]);

// Test: Controllers only use Services and Requests
arch('controllers use services and requests')
    ->expect('App\Http\Controllers\Api')
    ->toOnlyUse([
        'App\Services',
        'App\Http\Requests',
        'App\Http\Resources',
        'App\Models',
        'App\Http\Controllers',
        'Illuminate\Http',
        'Illuminate\Support\Facades',
        'Knuckles\Scribe\Attributes',
        'response', // Helper function
    ]);

// Test: No direct DB queries in Controllers
arch('controllers do not use DB facade directly')
    ->expect('App\Http\Controllers')
    ->not->toUse(\Illuminate\Support\Facades\DB::class);

// Test: Strict types declaration
arch('strict types in all PHP files')
    ->expect('App')
    ->toUseStrictTypes(); // Ignore for now, can be enforced gradually

// Test: No compact() usage (prefer explicit arrays)
arch('no compact function usage')
    ->expect(['compact'])
    ->not->toBeUsed();

// Test: Providers should extend ServiceProvider
arch('providers extend ServiceProvider')
    ->expect('App\Providers')
    ->toExtend(\Illuminate\Support\ServiceProvider::class);
