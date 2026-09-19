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

		return DeviceResource::collection(
			$query->paginate($perPage)
		);
	}

	public function store(DeviceStoreRequest $request): JsonResponse
	{
		$device = Device::create($request->validated());

		return response()->json(new DeviceResource($device), 201);
	}

	public function show(Device $device): DeviceResource
	{
		return new DeviceResource($device);
	}

	public function update(DeviceUpdateRequest $request, Device $device): JsonResponse
	{
		$device->update($request->validated());

		return response()->json(new DeviceResource($device));
	}

	public function destroy(Device $device): JsonResponse
	{
		if ($device->repairs()->exists()) {
			return response()->json([
				'message' => 'Нельзя удалить устройство, у которого есть ремонты.',
			], 409);
		}

		$device->delete();

		return response()->json(null, 204);
	}
}
