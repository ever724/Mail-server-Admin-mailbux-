<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Settings\TeamMember\Store;
use App\Http\Requests\Application\Settings\TeamMember\Update;
use App\Http\Resources\UserResource;
use App\Interfaces\TeamMemberInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamMemberController extends BaseController
{
    // Resource
    public $resource = UserResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param TeamMemberInterface $repository
     */
    public function __construct(TeamMemberInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Gate::authorize('view team members');

        $members = $this->repository->getPaginatedFilteredTeamMembers($request);

        return $this->sendCollectionResponse($members, true, 200);
    }

    /**
     * Store a newly created resource in database.
     *
     * @param Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request)
    {
        Gate::authorize('create team member');

        $member = $this->repository->createTeamMember($request);

        return $this->sendResponse($member, true, 201, [
            'messages' => __('messages.team_member_added'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        Gate::authorize('view team members');

        $member = $this->repository->newTeamMember($request);

        return $this->sendResponse($member, true, 200);
    }

    /**
     * Update the specified resource in database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Update $request)
    {
        Gate::authorize('update team member');

        $member = $this->repository->updateTeamMember($request, $request->member);

        return $this->sendResponse($member, true, 200, [
            'messages' => __('messages.team_member_updated'),
        ]);
    }

    /**
     * Delete the specified resource from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        Gate::authorize('delete team member');

        if ($this->repository->deleteTeamMember($request, $request->member)) {
            return $this->sendResponse([], true, 200, [
                'messages' => __('messages.team_member_deleted'),
            ]);
        }

        return $this->sendResponse([], false, 500, [
            'messages' => session()->get('alert-danger'),
        ]);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
