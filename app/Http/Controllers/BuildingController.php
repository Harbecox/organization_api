<?php

namespace App\Http\Controllers;

use App\Http\Resources\BuildingResource;
use App\Http\Resources\OrganizacionResource;
use App\Models\Building;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *      name="Buildings",
 *      description="Operations related to Buildings"
 *  )
 */
class BuildingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/buildings",
     *     summary="get buildings",
     *     tags={"Buildings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *          response=200,
     *          description="OK"
     *      )
     * )
     */
    function index()
    {
        $buildings = Building::all();
        return response()->json(BuildingResource::collection($buildings));
    }

    /**
     * @OA\Get(
     *     path="/api/buildings/{id}/organizations",
     *     summary="Get organization by building ID",
     *     tags={"Buildings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the building",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *     )
     * )
     */
    function getOrganizations($buildingId)
    {
        $organizations = Building::findOrFail($buildingId)->organizations->load('building')->load('activities');
        return response()->json(OrganizacionResource::collection($organizations));
    }
}
