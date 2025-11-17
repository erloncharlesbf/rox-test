<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Movie\DestroyMovieRequest;
use App\Http\Requests\Movie\IndexMovieRequest;
use App\Http\Requests\Movie\ShowMovieRequest;
use App\Http\Requests\Movie\StoreMovieRequest;
use App\Http\Requests\Movie\UpdateMovieRequest;
use App\Http\Resources\Movie\MovieResource;
use App\Models\Movie;
use App\Services\MovieService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(
    'Movies Management',
    'APIs for managing movies in your collection. All endpoints support full CRUD operations with advanced filtering and pagination.'
)]
#[Authenticated]
class MovieController extends Controller
{
    public function __construct(
        private readonly MovieService $movieService,
        private readonly ResponseFactory $responseFactory
    ) {}

    #[Endpoint(
        title: 'List all movies',
        description: 'Retrieve a paginated list of all movies in the collection. Supports filtering by multiple criteria and customizable pagination.'
    )]
    #[QueryParam('page', 'integer', 'Page number for pagination', example: 1)]
    #[QueryParam('per_page', 'integer', 'Number of items per page (default: 15, max: 100)', example: 15)]
    #[QueryParam('title', 'string', 'Filter movies by title (partial match)', required: false, example: 'Godfather')]
    #[QueryParam('director', 'string', 'Filter movies by director name (partial match)', required: false, example: 'Coppola')]
    #[QueryParam('genre', 'string', 'Filter movies by genre (exact match)', required: false, example: 'Crime')]
    #[QueryParam('status', 'string', 'Filter movies by availability status (available, unavailable, coming_soon)', required: false, example: 'available')]
    #[ResponseFromApiResource(MovieResource::class, Movie::class, collection: true, paginate: 15)]
    public function index(IndexMovieRequest $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $filters = $request->only(['title', 'director', 'genre', 'status']);

        $movies = $this->movieService->paginateMovies($perPage, $filters);

        return $this->responseFactory->json([
            'data' => MovieResource::collection($movies->load('cover')),
            'meta' => [
                'current_page' => $movies->currentPage(),
                'last_page' => $movies->lastPage(),
                'per_page' => $movies->perPage(),
                'total' => $movies->total(),
            ],
        ]);
    }

    #[Endpoint(
        title: 'Create a new movie',
        description: 'Add a new movie to the collection with all its details. Optionally upload a cover image (max 2MB, formats: jpeg, png, jpg, gif, webp).'
    )]
    #[BodyParam('title', 'string', 'The title of the movie', required: true, example: 'The Godfather')]
    #[BodyParam('director', 'string', 'The name of the movie director', required: true, example: 'Francis Ford Coppola')]
    #[BodyParam('description', 'string', 'A brief description or synopsis of the movie', required: false, example: 'The aging patriarch of an organized crime dynasty...')]
    #[BodyParam('studio', 'string', 'The name of the production studio', required: false, example: 'Paramount Pictures')]
    #[BodyParam('release_date', 'string', 'Date when the movie was released (YYYY-MM-DD)', required: false, example: '1972-03-24')]
    #[BodyParam('language', 'string', 'Two-letter ISO language code', required: false, example: 'en')]
    #[BodyParam('duration', 'integer', 'Duration of the movie in minutes', required: false, example: 175)]
    #[BodyParam('genre', 'string', 'The movie genre or category', required: false, example: 'Crime')]
    #[BodyParam('rating', 'number', 'Movie rating (0.0 to 10.0)', required: false, example: 9.2)]
    #[BodyParam('price', 'number', 'The price of the movie in USD', required: false, example: 12.99)]
    #[BodyParam('status', 'string', 'Current availability status: available, unavailable, or coming_soon', required: true, example: 'available')]
    #[BodyParam('cover', 'file', 'Cover/poster image file (max 2MB, jpeg/png/jpg/gif/webp)', required: false)]
    #[ResponseFromApiResource(MovieResource::class, Movie::class, status: 201)]
    public function store(StoreMovieRequest $request): JsonResponse
    {
        $movie = $this->movieService->createMovie($request->validated());

        return $this->responseFactory->json([
            'message' => 'Movie created successfully',
            'data' => MovieResource::make($movie),
        ], 201);
    }

    #[Endpoint(
        title: 'Get movie details',
        description: 'Retrieve detailed information about a specific movie by its ID, including cover image if available.'
    )]
    #[ResponseFromApiResource(MovieResource::class, Movie::class)]
    public function show(ShowMovieRequest $request, Movie $movie): JsonResponse
    {
        return $this->responseFactory->json([
            'data' => MovieResource::make($movie->load('cover')),
        ]);
    }

    #[Endpoint(
        title: 'Update movie information',
        description: 'Update one or more fields of an existing movie. All fields are optional. If a new cover image is provided, the old one will be replaced.'
    )]
    #[BodyParam('title', 'string', 'The title of the movie', required: false, example: 'The Godfather')]
    #[BodyParam('director', 'string', 'The name of the movie director', required: false, example: 'Francis Ford Coppola')]
    #[BodyParam('description', 'string', 'A brief description or synopsis of the movie', required: false)]
    #[BodyParam('studio', 'string', 'The name of the production studio', required: false, example: 'Paramount Pictures')]
    #[BodyParam('release_date', 'string', 'Date when the movie was released (YYYY-MM-DD)', required: false, example: '1972-03-24')]
    #[BodyParam('language', 'string', 'Two-letter ISO language code', required: false, example: 'en')]
    #[BodyParam('duration', 'integer', 'Duration of the movie in minutes', required: false, example: 175)]
    #[BodyParam('genre', 'string', 'The movie genre or category', required: false, example: 'Crime')]
    #[BodyParam('rating', 'number', 'Movie rating (0.0 to 10.0)', required: false, example: 9.2)]
    #[BodyParam('price', 'number', 'The price of the movie in USD', required: false, example: 12.99)]
    #[BodyParam('status', 'string', 'Current availability status', required: false, example: 'available')]
    #[BodyParam('cover', 'file', 'New cover/poster image file (replaces existing)', required: false)]
    #[ResponseFromApiResource(MovieResource::class, Movie::class)]
    public function update(UpdateMovieRequest $request, Movie $movie): JsonResponse
    {
        $updatedMovie = $this->movieService->updateMovie($movie, $request->validated());

        return $this->responseFactory->json([
            'message' => 'Movie updated successfully',
            'data' => MovieResource::make($updatedMovie),
        ]);
    }

    #[Endpoint(
        title: 'Delete a movie',
        description: 'Permanently remove a movie from the collection. This operation uses soft delete, so the movie can be restored if needed. Any associated cover image will also be deleted.'
    )]
    public function destroy(DestroyMovieRequest $request, Movie $movie): JsonResponse
    {
        $this->movieService->deleteMovie($movie);

        return $this->responseFactory->json([
            'message' => 'Movie deleted successfully',
        ], 204);
    }
}
