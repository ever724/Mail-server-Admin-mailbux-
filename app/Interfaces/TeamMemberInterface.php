<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface TeamMemberInterface
{
    public function getPaginatedFilteredTeamMembers(Request $request);

    public function newTeamMember(Request $request);

    public function createTeamMember(Request $request);

    public function getTeamMemberById(Request $request, $member_id);

    public function updateTeamMember(Request $request, $member_id);

    public function deleteTeamMember(Request $request, $member_id);
}
