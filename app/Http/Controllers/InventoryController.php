<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryUserPivot;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * show all inventories.
     */
    public function getAll()
    {
        $userId = auth()->id();
        $inventoryUserPivotData = InventoryUserPivot::where('user_id', $userId)->get();
        $validInventoryIds = [];

        if (count($inventoryUserPivotData) > 0) {
            foreach ($inventoryUserPivotData as $value) {
                array_push($validInventoryIds, $value->inventory_id);
            }
        } else {
            return response()->json([
                "status_code" => 500,
                "message" => "No records to show!",
                500
            ]);
        }

        $inventories = Inventory::all();
        $inventoryData = [];

        if (count($inventories) > 0) {

            foreach ($inventories as $inventory) {
                foreach ($validInventoryIds as $validInventoryId) {
                    if ($validInventoryId == $inventory->id) {
                        $object = [
                            "id" => $inventory->id,
                            "name" => $inventory->name,
                            "description" => $inventory->description
                        ];
                        array_push($inventoryData, $object);
                    }
                }
            }

            return response()->json([
                "status_code" => 200,
                "results" => $inventoryData,
                200
            ]);
        } else {
            return response()->json([
                "status_code" => 500,
                "message" => "No records to show!",
                500
            ]);
        }
    }

    /**
     * Create a new Inventory
     */
    public function create(Request $request)
    {
        $validatedData = validator(
            $request->only(
                'name',
                'description',
            ),
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]
        );

        if ($validatedData->fails()) {
            return response()->json([
                "status_code" => 400,
                "message" => $validatedData->errors()->all(),
                400
            ]);
        }

        $inventoryData = [
            "name" => $request->name,
            "description" => $request->description
        ];

        $inventory = Inventory::create($inventoryData);
        $userId = auth()->id();

        if ($inventory) {

            $inventoryUserPivotData = [
                "user_id" => $userId,
                "inventory_id" => $inventory->id
            ];

            $inventoryUserPivot = InventoryUserPivot::create($inventoryUserPivotData);

            if ($inventoryUserPivot) {

                return response()->json([
                    "status_code" => 201,
                    "message" => "Inventory successfully created!",
                    201
                ]);
            } else {
                return response()->json([
                    "status_code" => 400,
                    "message" => "Failed to create inventory!",
                    400
                ]);
            }
        } else {
            return response()->json([
                "status_code" => 400,
                "message" => "Failed to create inventory!",
                400
            ]);
        }
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validatedData = validator(
            $request->only(
                'name',
                'description',
            ),
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]
        );

        if ($validatedData->fails()) {
            return response()->json([
                "status_code" => 400,
                "message" => $validatedData->errors()->all(),
                400
            ]);
        }

        if (isset($request->name)) {
            $inventory->name = $request->name;
        }

        if (isset($request->description)) {
            $inventory->description = $request->description;
        }

        if ($inventory->update()) {

            return response()->json([
                "status_code" => 200,
                "message" => "Inventory successfully updated!",
                200
            ]);
        } else {
            return response()->json([
                "status_code" => 400,
                "message" => "Failed to update inventory!",
                400
            ]);
        }
    }

    /**
     * Remove a specified inventory.
     */
    public function delete(Inventory $inventory)
    {
        $inventoryUserPivotData = InventoryUserPivot::where('inventory_id', $inventory->id)->first();
        if ($inventory->delete()) {
            if ($inventoryUserPivotData->delete()) {
                return response()->json([
                    "status_code" => 200,
                    "message" => "Inventory successfully deleted!",
                    200
                ]);
            } else {
                return response()->json([
                    "status_code" => 400,
                    "message" => "Failed to delete inventory!",
                    400
                ]);
            }
        } else {
            return response()->json([
                "status_code" => 400,
                "message" => "Failed to delete inventory!",
                400
            ]);
        }
    }
}
