<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceIndexRequest;
use App\Http\Requests\DeviceStoreRequest;
use App\Http\Requests\DeviceUpdateRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DeviceController extends Controller
{
    public function index(DeviceIndexRequest $request): AnonymousResourceCollection
    {
        $data = $request->validated();

        $perPage = $data['per_page'] ?? 9;
        $search = $data['search'] ?? null;
        $clientId = $data['client_id'] ?? null;

        $query = Device::query()->with('client')->orderByDesc('created_at');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        return DeviceResource::collection($query->paginate($perPage));
    }

    public function store(DeviceStoreRequest $request): JsonResponse
    {
        $device = Device::create($request->validated());

        return (new DeviceResource($device))->toResponse($request)->setStatusCode(201);
    }

    public function show(Device $device): DeviceResource
    {
        return new DeviceResource($device);
    }

    public function update(DeviceUpdateRequest $request, Device $device): DeviceResource
    {
        $device->update($request->validated());

        return new DeviceResource($device);
    }

    public function destroy(Device $device): Response|JsonResponse
    {
        if ($device->repairs()->exists()) {
            return response()->json([
                'message' => 'Нельзя удалить устройство, у которого есть ремонты.',
            ], 409);
        }

        $device->delete();

        return response()->noContent();
    }
}
