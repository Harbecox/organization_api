<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizacionResource;
use App\Models\Activity;
use Illuminate\Support\Collection;

/**
 * @OA\Tag(
 *      name="Activities",
 *      description="Operations related to activities"
 *  )
 */

class ActivityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/activities",
     *     summary="get activities tree",
     *     tags={"Activities"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *          response=200,
     *          description="OK"
     *      )
     * )
     */
    function index()
    {
        function getActivitiesTree($activities)
        {
            return $activities->map(function ($activity) {
                $activity->children = getActivitiesTree($activity->children);
                return $activity;
            });
        }

        $rootActivities = Activity::with('children')
            ->whereNull('parent_id')
            ->get();

        $tree = getActivitiesTree($rootActivities);

        return response()->json($tree);
    }

    /**
     * @OA\Get(
     *     path="/api/activities/{id}/organizations",
     *     summary="Get organization by activity ID",
     *     tags={"Activities"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the activity",
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

    function getOrganizations($activityId)
    {
        $activity = Activity::find($activityId);
        $organizations = new Collection();
        $this->mapActivityTree($activity,$organizations);
        return response()->json(OrganizacionResource::collection($organizations));
    }

    private function mapActivityTree($activity,Collection &$organizations){
        $organizations = $organizations->merge($activity->organizations->load('building')->load('activities'));
        if($activity->children){
            foreach ($activity->children as $child){
                $this->mapActivityTree($child,$organizations);
            }
        }
    }
}
