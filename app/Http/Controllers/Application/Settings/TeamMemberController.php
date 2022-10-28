<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\TeamMember\Store;
use App\Http\Requests\Application\Settings\TeamMember\Update;
use App\Interfaces\TeamMemberInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamMemberController extends Controller
{
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
     * Display Team Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view team members');

        return view('application.settings.team.index');
    }

    /**
     * Display the Form for Creating New Team Member.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create team member');

        return view('application.settings.team.create_member', [
            'member' => $this->repository->newTeamMember($request),
        ]);
    }

    /**
     * Store the Team Member in Database.
     *
     * @param \App\Http\Requests\Application\Settings\TeamMember\Store $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        Gate::authorize('create team member');

        $this->repository->createTeamMember($request);

        session()->flash('alert-success', __('messages.team_member_added'));

        return redirect()->route('settings.team', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Team Member.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update team member');

        return view('application.settings.team.edit_member', [
            'member' => $this->repository->getTeamMemberById($request, $request->member),
        ]);
    }

    /**
     * Update the Team Member.
     *
     * @param \App\Http\Requests\Application\Settings\TeamMember\Update $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request)
    {
        Gate::authorize('update team member');

        $this->repository->updateTeamMember($request, $request->member);

        session()->flash('alert-success', __('messages.team_member_updated'));

        return redirect()->route('settings.team', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Team Member.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete team member');

        if ($this->repository->deleteTeamMember($request, $request->member)) {
            session()->flash('alert-success', __('messages.team_member_deleted'));

            return redirect()->route('settings.team', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
