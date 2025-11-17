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

use App\Contracts\Repositories\BaseRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\Book\BookResource;
use App\Http\Resources\Movie\MovieResource;
use App\Models\Book;
use App\Models\Movie;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

arch('no debug functions in production code')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'var_export', 'die', 'exit'])
    ->not->toBeUsed();

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

arch('no ray debugging in production')
    ->expect(['ray'])
    ->not->toBeUsed();

arch('controllers')
    ->expect('App\Http\Controllers')
    ->toBeClasses()
    ->toHaveSuffix('Controller')
    ->ignoring(Controller::class);

arch('models')
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(Model::class)
    ->ignoring(User::class);

arch('repositories')
    ->expect('App\Repositories')
    ->toBeClasses()
    ->toHaveSuffix('Repository');

arch('services')
    ->expect('App\Services')
    ->toBeClasses()
    ->toHaveSuffix('Service');

arch('requests')
    ->expect('App\Http\Requests')
    ->toBeClasses()
    ->toExtend(FormRequest::class);

arch('resources should extend JsonResource or ResourceCollection')
    ->expect('App\Http\Resources')
    ->toBeClasses()
    ->toExtendNothing()
    ->ignoring([
        AttachmentResource::class,
        BookResource::class,
        MovieResource::class,
    ]);

arch('repositories implement interfaces')
    ->expect('App\Repositories')
    ->toImplement(BaseRepositoryInterface::class)
    ->ignoring(BaseRepository::class);

arch('controllers are classes')
    ->expect('App\Http\Controllers')
    ->toBeClasses();

arch('models use SoftDeletes when needed')
    ->expect(Movie::class)
    ->toUse(SoftDeletes::class);

arch('models use SoftDeletes for Book')
    ->expect(Book::class)
    ->toUse(SoftDeletes::class);

arch('no eval usage')
    ->expect(['eval'])
    ->not->toBeUsed();

arch('no global variables in classes')
    ->expect('App')
    ->not->toUse(['global']);

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
        'Illuminate\Filesystem',
        'Illuminate\Http',
        'Illuminate\Contracts',
        'Illuminate\Auth',
    ]);

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
        'Illuminate\Contracts',
        'Illuminate\Auth',
        'Knuckles\Scribe\Attributes',
        'response',
    ]);

arch('controllers do not use DB facade directly')
    ->expect('App\Http\Controllers')
    ->not->toUse(DB::class);

arch('strict types in all PHP files')
    ->expect('App')
    ->toUseStrictTypes();

arch('no compact function usage')
    ->expect(['compact'])
    ->not->toBeUsed();

arch('providers extend ServiceProvider')
    ->expect('App\Providers')
    ->toExtend(ServiceProvider::class);
