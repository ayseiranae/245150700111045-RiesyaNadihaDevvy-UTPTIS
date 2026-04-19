<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Items API",
    version: "1.0.0",
    description: "API documentation for managing Items. This API provides CRUD operations for pastry shop items stored in a JSON file.",
    contact: new OA\Contact(
        name: "Riesya Nadiha Devvy",
        email: "riesyanadiha@student.ub.ac.id"
    )
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Local Development Server"
)]
class ItemController extends Controller
{
    private $path;

    public function __construct()
    {
        $this->path = storage_path('app/items.json');
    }

    private function response($status, $message, $data = null, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    #[OA\Get(
        path: "/api/items",
        summary: "Get all items",
        description: "Retrieve a list of all items. Supports filtering by category and searching by name.",
        operationId: "getItems",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "category",
                in: "query",
                required: false,
                description: "Filter items by category",
                schema: new OA\Schema(type: "string", example: "Pastry")
            ),
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                description: "Search items by name (case-insensitive)",
                schema: new OA\Schema(type: "string", example: "Croissant")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Items retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Items retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 2),
                                    new OA\Property(property: "name", type: "string", example: "Pain au Chocolat"),
                                    new OA\Property(property: "category", type: "string", example: "Pastry"),
                                    new OA\Property(property: "price", type: "number", example: 20000),
                                    new OA\Property(property: "stock", type: "integer", example: 20),
                                    new OA\Property(property: "description", type: "string", example: "Pastry filled with premium chocolate")
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function index(Request $request)
    {
        $data = json_decode(file_get_contents($this->path), true);

        if ($request->category) {
            $data = array_filter($data, function ($item) use ($request) {
                return strtolower($item['category']) == strtolower($request->category);
            });
        }

        if ($request->search) {
            $data = array_filter($data, function ($item) use ($request) {
                return strpos(strtolower($item['name']), strtolower($request->search)) !== false;
            });
        }

        return $this->response('success', 'Items retrieved successfully', array_values($data));
    }

    #[OA\Get(
        path: "/api/items/{id}",
        summary: "Get item by ID",
        description: "Retrieve a single item by its ID.",
        operationId: "getItemById",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID of the item to retrieve",
                schema: new OA\Schema(type: "integer", example: 2)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Item found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Item found"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 2),
                                new OA\Property(property: "name", type: "string", example: "Pain au Chocolat"),
                                new OA\Property(property: "category", type: "string", example: "Pastry"),
                                new OA\Property(property: "price", type: "number", example: 20000),
                                new OA\Property(property: "stock", type: "integer", example: 20),
                                new OA\Property(property: "description", type: "string", example: "Pastry filled with premium chocolate")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Item not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "error"),
                        new OA\Property(property: "message", type: "string", example: "Item with ID 99 not found"),
                        new OA\Property(property: "data", type: "string", nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function show($id)
    {
        $data = json_decode(file_get_contents($this->path), true);

        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return $this->response('success', 'Item found', $item);
            }
        }

        return $this->response('error', "Item with ID $id not found", null, 404);
    }

    #[OA\Post(
        path: "/api/items",
        summary: "Create a new item",
        description: "Create a new item with the provided data. All fields are required.",
        operationId: "createItem",
        tags: ["Items"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Item data to create",
            content: new OA\JsonContent(
                required: ["name", "category", "price", "stock", "description"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Almond Croissant"),
                    new OA\Property(property: "category", type: "string", example: "Pastry"),
                    new OA\Property(property: "price", type: "number", example: 25000, description: "Minimum value: 1000"),
                    new OA\Property(property: "stock", type: "integer", example: 12, description: "Minimum value: 0"),
                    new OA\Property(property: "description", type: "string", example: "Croissant filled with almond cream and topped with sliced almonds")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Item created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Item created successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 7),
                                new OA\Property(property: "name", type: "string", example: "Almond Croissant"),
                                new OA\Property(property: "category", type: "string", example: "Pastry"),
                                new OA\Property(property: "price", type: "number", example: 25000),
                                new OA\Property(property: "stock", type: "integer", example: 12),
                                new OA\Property(property: "description", type: "string", example: "Croissant filled with almond cream and topped with sliced almonds")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The name field is required."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "name",
                                    type: "array",
                                    items: new OA\Items(type: "string", example: "The name field is required.")
                                )
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric|min:1000',
            'stock' => 'required|numeric|min:0',
            'description' => 'required'
        ]);

        $data = json_decode(file_get_contents($this->path), true);

        $lastId = !empty($data) ? end($data)['id'] : 0;
        $newId = $lastId + 1;

        $newItem = [
            'id' => $newId,
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description
        ];

        $data[] = $newItem;

        file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));

        return $this->response('success', 'Item created successfully', $newItem);
    }

    #[OA\Put(
        path: "/api/items/{id}",
        summary: "Update an item (full update)",
        description: "Replace all fields of an existing item. All fields are required.",
        operationId: "updateItem",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID of the item to update",
                schema: new OA\Schema(type: "integer", example: 4)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Complete item data for update",
            content: new OA\JsonContent(
                required: ["name", "category", "price", "stock", "description"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Cinnamon Roll Special"),
                    new OA\Property(property: "category", type: "string", example: "Sweet Bread"),
                    new OA\Property(property: "price", type: "number", example: 22000, description: "Minimum value: 1000"),
                    new OA\Property(property: "stock", type: "integer", example: 10, description: "Minimum value: 0"),
                    new OA\Property(property: "description", type: "string", example: "Special cinnamon roll with extra cream cheese frosting")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Item updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Item updated successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 4),
                                new OA\Property(property: "name", type: "string", example: "Cinnamon Roll Special"),
                                new OA\Property(property: "category", type: "string", example: "Sweet Bread"),
                                new OA\Property(property: "price", type: "number", example: 22000),
                                new OA\Property(property: "stock", type: "integer", example: 10),
                                new OA\Property(property: "description", type: "string", example: "Special cinnamon roll with extra cream cheese frosting")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Item not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "error"),
                        new OA\Property(property: "message", type: "string", example: "Item with ID 99 not found"),
                        new OA\Property(property: "data", type: "string", nullable: true, example: null)
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The name field is required."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "name",
                                    type: "array",
                                    items: new OA\Items(type: "string", example: "The name field is required.")
                                )
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric|min:1000',
            'stock' => 'required|numeric|min:0',
            'description' => 'required'
        ]);

        $data = json_decode(file_get_contents($this->path), true);

        foreach ($data as $index => $item) {
            if ($item['id'] == $id) {

                $data[$index] = [
                    'id' => $id,
                    'name' => $request->name,
                    'category' => $request->category,
                    'price' => $request->price,
                    'stock' => $request->stock,
                    'description' => $request->description
                ];

                file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));

                return $this->response('success', 'Item updated successfully', $data[$index]);
            }
        }

        return $this->response('error', "Item with ID $id not found", null, 404);
    }

    #[OA\Patch(
        path: "/api/items/{id}",
        summary: "Partially update an item",
        description: "Update one or more fields of an existing item. Only provided fields will be updated.",
        operationId: "patchItem",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID of the item to partially update",
                schema: new OA\Schema(type: "integer", example: 3)
            )
        ],
        requestBody: new OA\RequestBody(
            required: false,
            description: "Partial item data for update (only include fields you want to change)",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Strawberry Danish Deluxe"),
                    new OA\Property(property: "category", type: "string", example: "Pastry"),
                    new OA\Property(property: "price", type: "number", example: 28000),
                    new OA\Property(property: "stock", type: "integer", example: 8),
                    new OA\Property(property: "description", type: "string", example: "Deluxe pastry topped with fresh strawberries and vanilla custard")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Item partially updated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Item partially updated"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 3),
                                new OA\Property(property: "name", type: "string", example: "Strawberry Danish Deluxe"),
                                new OA\Property(property: "category", type: "string", example: "Pastry"),
                                new OA\Property(property: "price", type: "number", example: 28000),
                                new OA\Property(property: "stock", type: "integer", example: 8),
                                new OA\Property(property: "description", type: "string", example: "Deluxe pastry topped with fresh strawberries and vanilla custard")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Item not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "error"),
                        new OA\Property(property: "message", type: "string", example: "Item with ID 99 not found"),
                        new OA\Property(property: "data", type: "string", nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function patch(Request $request, $id)
    {
        $data = json_decode(file_get_contents($this->path), true);

        foreach ($data as $index => $item) {
            if ($item['id'] == $id) {

                $data[$index]['name'] = $request->name ?? $item['name'];
                $data[$index]['category'] = $request->category ?? $item['category'];
                $data[$index]['price'] = $request->price ?? $item['price'];
                $data[$index]['stock'] = $request->stock ?? $item['stock'];
                $data[$index]['description'] = $request->description ?? $item['description'];

                file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));

                return $this->response('success', 'Item partially updated', $data[$index]);
            }
        }

        return $this->response('error', "Item with ID $id not found", null, 404);
    }

    #[OA\Delete(
        path: "/api/items/{id}",
        summary: "Delete an item",
        description: "Delete an item by its ID.",
        operationId: "deleteItem",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID of the item to delete",
                schema: new OA\Schema(type: "integer", example: 6)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Item deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Item deleted successfully"),
                        new OA\Property(property: "data", type: "string", nullable: true, example: null)
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Item not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "error"),
                        new OA\Property(property: "message", type: "string", example: "Item with ID 99 not found"),
                        new OA\Property(property: "data", type: "string", nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function destroy($id)
    {
        $data = json_decode(file_get_contents($this->path), true);

        foreach ($data as $index => $item) {
            if ($item['id'] == $id) {

                array_splice($data, $index, 1);

                file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));

                return $this->response('success', 'Item deleted successfully');
            }
        }

        return $this->response('error', "Item with ID $id not found", null, 404);
    }
}
