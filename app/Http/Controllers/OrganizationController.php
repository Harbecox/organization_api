<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizacionResource;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *      name="Organizations",
 *      description="Operations related to Organizations"
 *  )
 */

class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/organizations",
     *     summary="get organizations",
     *     tags={"Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *          response=200,
     *          description="OK"
     *      )
     * )
     */
    function index()
    {
        $organizations = Organization::query()->with('building')->with('activities')->get();
        return response()->json(OrganizacionResource::collection($organizations));
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/{id}",
     *     summary="Get organization by ID",
     *     tags={"Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the organization",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK"
     *     )
     * )
     */

    function show($organizationId)
    {
        $organization = Organization::findOrFail($organizationId)->load('building')->load('activities');
        return response()->json(OrganizacionResource::make($organization));
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/name/{name}",
     *     summary="Find organization by name",
     *     tags={"Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="name of the organization",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *     )
     * )
     */

    function findByName($name)
    {
        $organizations = Organization::query()->where('name', 'like', "%$name%")->with('building')->with('activities')->get();
        return response()->json(OrganizacionResource::collection($organizations));
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/find-in-radius",
     *     summary="Find organizations within a radius",
     *     tags={"Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         required=true,
     *         description="Latitude of the central point",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=40.73061
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         required=true,
     *         description="Longitude of the central point",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=-73.935242
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         required=false,
     *         description="Search radius in kilometers (default: 10)",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *     )
     * )
     */

    function findInRadius(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $radius = $request->get('radius', 10);
        $organizations = Organization::select('organizations.*')
            ->join('buildings', 'organizations.building_id', '=', 'buildings.id')
            ->selectRaw("
        (6371 * acos(
            cos(radians(?)) * cos(radians(buildings.latitude)) * cos(radians(buildings.longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(buildings.latitude))
        )) AS distance
    ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->with(['building','activities'])
            ->get();

        return response()->json(OrganizacionResource::collection($organizations));
    }


    /**
     * @OA\Get(
     *     path="/api/organizations/find-in-rectangle",
     *     summary="Find organizations within a rectangular area",
     *     tags={"Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="north_west_latitude",
     *         in="query",
     *         required=true,
     *         description="Latitude of the north-west corner of the rectangle",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=40.73061
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="north_west_longitude",
     *         in="query",
     *         required=true,
     *         description="Longitude of the north-west corner of the rectangle",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=-73.935242
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="south_east_latitude",
     *         in="query",
     *         required=true,
     *         description="Latitude of the south-east corner of the rectangle",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=40.712776
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="south_east_longitude",
     *         in="query",
     *         required=true,
     *         description="Longitude of the south-east corner of the rectangle",
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example=-74.005974
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *     )
     * )
     */

    function findInRectangle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'north_west_latitude' => 'required|numeric|between:-90,90',
            'north_west_longitude' => 'required|numeric|between:-180,180',
            'south_east_latitude' => 'required|numeric|between:-90,90',
            'south_east_longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $northWestLat = $request->get('north_west_latitude');
        $northWestLng = $request->get('north_west_longitude');
        $southEastLat = $request->get('south_east_latitude');
        $southEastLng = $request->get('south_east_longitude');

        $organizations = Organization::select('organizations.*')
            ->join('buildings', 'organizations.building_id', '=', 'buildings.id')
            ->whereBetween('buildings.latitude', [$northWestLat,$southEastLat])
            ->whereBetween('buildings.longitude', [$northWestLng, $southEastLng])
            ->with(['building', 'activities'])
            ->get();

        return response()->json(OrganizacionResource::collection($organizations));
    }

}
