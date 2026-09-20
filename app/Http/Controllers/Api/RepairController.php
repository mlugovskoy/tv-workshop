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
use App\Models\RepairStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
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

            $repair = Repair::create([
                'client_id' => $client->id,
                'device_id' => $device->id,
                'status' => 'NEW',
                'problem_description' => $data['problem_description'],
                'estimated_price' => $data['estimated_price'],
                'received_at' => now()
            ]);

            RepairStatusHistory::create([
                'repair_id' => $repair->id,
                'status' => RepairStatus::NEW
            ]);

            return $repair;
        });


        return (new RepairResource($repair))->toResponse($request)->setStatusCode(201);
    }

    public function show(Repair $repair): RepairResource
    {
        return new RepairResource($repair->load(['client', 'device', 'statusHistories']));
    }

    public function update(RepairUpdateRequest $request, Repair $repair): RepairResource
    {
        $repair->update($request->validated());

        return new RepairResource($repair);
    }

    public function changeStatus(ChangeRepairStatusRequest $request, Repair $repair): RepairResource
    {
        DB::transaction(function () use ($request, $repair) {
            $status = RepairStatus::from($request->validated()['status']);

            $repair->status = $status;

            if($status === RepairStatus::READY) {
               $repair->completed_at ??= now();
               $repair->issued_at = null;
            } elseif($status === RepairStatus::ISSUED) {
                $repair->completed_at ??= now();
                $repair->issued_at ??= now();

                if($repair->final_price === null && $repair->estimated_price !== null) {
                    $repair->final_price = $repair->estimated_price;
                }
            } else {
                $repair->completed_at = null;
                $repair->issued_at = null;
            }

            $repair->save();

            RepairStatusHistory::create([
                'repair_id' => $repair->id,
                'status' => $repair->status
            ]);
        });

        $repair->refresh();

        return new RepairResource($repair->load(['client', 'device', 'statusHistories']));
    }

    public function destroy(Repair $repair): Response
    {
        $repair->delete();

        return response()->noContent();
    }
}
