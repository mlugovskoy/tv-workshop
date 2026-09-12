<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientIndexRequest;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    public function index(ClientIndexRequest $request): AnonymousResourceCollection
    {
        $data = $request->validated();

        $perPage = $data['per_page'] ?? 9;

        return ClientResource::collection(
            Client::query()
                ->orderByDesc('created_at')
                ->paginate($perPage)
        );
    }

    public function store(ClientStoreRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return response()->json(new ClientResource($client), 201);
    }

    public function show(Client $client): ClientResource
    {
        return new ClientResource($client);
    }

    public function update(ClientUpdateRequest $request, Client $client): JsonResponse
    {
        $client->update($request->validated());

        return response()->json(new ClientResource($client));
    }

    public function destroy(Client $client): Response
    {
        $client->delete();

        return response()->noContent();
    }
}
