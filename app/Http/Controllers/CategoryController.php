<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/categories",
     * tags={"Categories"},
     * summary="Get all categories",
     * description="Retrieve a list of all book categories along with the count of books in each category.",
     * @OA\Response(
     * response=200,
     * description="Successful response",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Category")
     * )
     * )
     * )
     */
    public function index()
    {
        $categories = Category::withCount('books')->get();
        return response()->json([
            'categories' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/categories",
     * tags={"Categories"},
     * summary="Create a new category Only for Admins ",
     * description="Only administrators can create new categories.",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="History"),
     * @OA\Property(property="description", type="string", example="A category for historical books")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Category created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(
     * response=401,
     * description="You must be logged in first (Unauthenticated)"
     * ),
     * @OA\Response(
     * response=403,
     * description="You are not authorized to perform this action (Forbidden)"
     * )
     * )
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => new CategoryResource($category),
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/categories/{id}",
     * tags={"Categories"},
     * summary="Get a category by ID",
     * description="Retrieve details of a specific category by its ID.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the category to retrieve",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful response",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(
     * response=404,
     * description="Category not found"
     * )
     * )
     */
    public function show(Category $category)
    {
        return response()->json([
            'category' => new CategoryResource($category)
        ], 200);
    }

    /**
     * @OA\Put(
     * path="/api/categories/{id}",
     * tags={"Categories"},
     * summary="Update a category Only for Admins ",
     * description="Only administrators can update categories.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the category to update",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="Updated Category Name")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Category updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(
     * response=401,
     * description="You must be logged in first (Unauthenticated)"
     * ),
     * @OA\Response(
     * response=403,
     * description="You are not authorized to perform this action (Forbidden)"
     * ),
     * @OA\Response(
     * response=404,
     * description="Category not found"
     * )
     * )
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => new CategoryResource($category),
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/api/categories/{id}",
     * tags={"Categories"},
     * summary="Delete a category Only for Admins ",
     * description="Only administrators can delete categories.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the category to delete",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Category deleted successfully"
     * ),
     * @OA\Response(
     * response=401,
     * description="You must be logged in first (Unauthenticated)"
     * ),
     * @OA\Response(
     * response=403,
     * description="You are not authorized to perform this action (Forbidden)"
     * ),
     * @OA\Response(
     * response=404,
     * description="Category not found"
     * )
     * )
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
