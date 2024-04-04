<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\InventoryUserPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;

class InventoryItemController extends Controller
{
    /**
     * show all inventory items
     */
    public function getAll(Inventory $inventory)
    {
        $userId = auth()->id();
        $inventoryUserPivotData = InventoryUserPivot::where('user_id', $userId)->get();
        $isValid = false;

        if (count($inventoryUserPivotData) > 0) {
            foreach ($inventoryUserPivotData as $value) {
                if ($value->inventory_id == $inventory->id) {
                    $isValid = true;
                }
            }
        }

        if ($isValid == false) {
            return response()->json([
                "status_code" => 401,
                "message" => "Unauthorized access",
                400
            ]);
        }

        $inventoryItemList = InventoryItem::where('inventory_id', $inventory->id)->get();
        $inventoryItemData = [];

        if (count($inventoryItemList) > 0) {
            foreach ($inventoryItemList as $value) {
                $object = [
                    "id" => $value->id,
                    "inventoryId" => $value->inventory_id,
                    "name" => $value->name,
                    "description" => $value->description,
                    "image" => $value->image,
                    "quantity" => $value->quantity,
                ];
                array_push($inventoryItemData, $object);
            }
            return response()->json([
                "status_code" => 200,
                "results" => $inventoryItemData,
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
     * Create a new inventory Item
     */
    public function create(Request $request)
    {
        $validatedData = validator(
            $request->only(
                'inventoryId',
                'name',
                'description',
                'image',
                'quantity'
            ),
            [
                'inventoryId' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable',
                'quantity' => 'required'
            ]
        );

        if ($validatedData->fails()) {
            return response()->json([
                "status_code" => 400,
                "message" => $validatedData->errors()->all(),
                400
            ]);
        }


        $productImage = $request->file('image');
        $productImagePath = null;
        if (!empty($productImage)) {
            // dd('hello' );
            $path = public_path() . '/media/images/';
            if (!FacadesFile::isDirectory($path)) {
                Storage::makeDirectory($path, $mode = 0777, true, true);
            }
            $imageName = time() . '_'  . $request->name . '.' . $productImage->extension();
            $productImage->move($path, $imageName);
            $productImagePath = $path . $imageName;
        }

        $inventoryItemData = [
            'inventory_id' => $request->inventoryId,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $productImagePath,
            'quantity' => $request->quantity
        ];

        $inventoryItem = InventoryItem::create($inventoryItemData);

        if ($inventoryItem) {

            return response()->json([
                "status_code" => 201,
                "message" => "Inventory item successfully created!",
                201
            ]);
        } else {
            return response()->json([
                "status_code" => 400,
                "message" => "Failed to create inventory item!",
                400
            ]);
        }
    }

    /**
     * Display a specified inventory item.
     */
    public function show(InventoryItem $inventoryItem)
    {
        $userId = auth()->id();
        $inventoryUserPivotData = InventoryUserPivot::where('user_id', $userId)->get();
        $isValid = false;

        if (count($inventoryUserPivotData) > 0) {
            foreach ($inventoryUserPivotData as $value) {
                if ($value->inventory_id == $inventoryItem->inventory_id) {
                    $isValid = true;
                }
            }
        }

        if ($isValid == false) {
            return response()->json([
                "status_code" => 401,
                "message" => "Unauthorized access",
                400
            ]);
        }

        return response()->json([
            "status_code" => 200,
            "results" => [
                "id" => $inventoryItem->id,
                "inventoryId" => $inventoryItem->inventory_id,
                "name" => $inventoryItem->name,
                "description" => $inventoryItem->description,
                "quantity" => $inventoryItem->quantity,
                "image" => $inventoryItem->image
            ],
            400
        ]);
    }

    /**
     * Update a specified inventory item.
     */
    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validatedData = validator(
            $request->only(
                'inventoryId',
                'name',
                'description',
                'image',
                'quantity'
            ),
            [
                'inventoryId' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable',
                'quantity' => 'required'
            ]
        );

        if ($validatedData->fails()) {
            return response()->json([
                "status_code" => 400,
                "message" => $validatedData->errors()->all(),
                400
            ]);
        }


        $productImage = $request->file('image');
        $productImagePath = null;
        if (!empty($productImage)) {
            // dd('hello' );
            $path = public_path() . '/media/images/';
            if (!FacadesFile::isDirectory($path)) {
                Storage::makeDirectory($path, $mode = 0777, true, true);
            }
            $imageName = time() . '_'  . $request->name . '.' . $productImage->extension();
            $productImage->move($path, $imageName);
            $productImagePath = $path . $imageName;
        }

        if (isset($request->name)) {
            $inventoryItem->name = $request->name;
        }

        if (isset($request->description)) {
            $inventoryItem->description = $request->description;
        }

        if (isset($request->quantity)) {
            $inventoryItem->quantity = $request->quantity;
        }

        if (!empty($request->file('image'))) {
            $inventoryItem->image = $productImagePath;
        }

        if ($inventoryItem->update()) {

            return response()->json([
                "status_code" => 200,
                "message" => "Inventory item successfully updated!",
                200
            ]);
        } else {
            return response()->json([
                "status_code" => 400,
                "message" => "Failed to update inventory item!",
                400
            ]);
        }
    }

    /**
     * Remove a specified inventory item.
     */
    public function delete(InventoryItem $inventoryItem)
    {
        if ($inventoryItem->delete()) {

            return response()->json([
                "status_code" => 200,
                "message" => "Inventory item successfully deleted!",
                200
            ]);
        } else {

            return response()->json([
                "status_code" => 400,
                "message" => "Failed to delete inventory item!",
                400
            ]);
        }
    }
}
