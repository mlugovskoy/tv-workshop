<?php

namespace App\Http\Controllers\Api;

use App\Enums\RepairStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeRepairStatusRequest;
use App\Http\Requests\RepairIndexRequest;
use App\Http\Requests\RepairStoreRequest;
use App\Http\Requests\RepairUpdateRequest;
use App\Http\Resources\RepairResource;
use App\Models\Client;
use App\Models\Device;
use App\Models\Repair;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RepairController extends Controller
{
    public function index(RepairIndexRequest $request): AnonymousResourceCollection
    {
        $data = $request->validated();

        $perPage = $data['per_page'] ?? 9;

        $repairs = Repair::query()
            ->with(['client', 'device'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return RepairResource::collection($repairs);
    }

    public function store(RepairStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $repair = DB::transaction(function () use ($data) {
            if (!empty($data['client_id'])) {
                $client = Client::query()->findOrFail($data['client_id']);
            } else {
                $client = Client::create([
                    'name' => $data['client_name'],
                    'phone' => $data['phone']
                ]);
            }

            if (!empty($data['device_id'])) {
                $device = Device::query()->findOrFail($data['device_id']);

                if ($device->client_id != $client->id) {
                    abort(409, 'Устройство не принадлежит выбранному клиенту');
                }
            } else {
                $device = Device::create([
                    'client_id' => $client->id,
                    'brand' => $data['brand'],
                    'model' => $data['model'],
                ]);
            }

            return Repair::create([
                'client_id' => $client->id,
                'device_id' => $device->id,
                'status' => 'NEW',
                'problem_description' => $data['problem_description'],
                'estimated_price' => $data['estimated_price'],
                'received_at' => now()
            ]);
        });

        return response()->json(new RepairResource($repair), 201);
    }

    public function show(Repair $repair): RepairResource
    {
        return new RepairResource($repair);
    }

    public function update(RepairUpdateRequest $request, Repair $repair): JsonResponse
    {
        $repair->update($request->validated());

        return response()->json(new RepairResource($repair));
    }

    public function changeStatus(ChangeRepairStatusRequest $request, Repair $repair): JsonResponse
    {
        $repair->status = RepairStatus::from($request->validated()['status']);

        $repair->save();

        return response()->json(new RepairResource($repair));
    }

    public function destroy(Repair $repair): JsonResponse
    {
        $repair->delete();

        return response()->json(null, 204);
    }
}
