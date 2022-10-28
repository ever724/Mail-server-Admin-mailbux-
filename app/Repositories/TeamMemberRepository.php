<?php

namespace App\Repositories;

use App\Interfaces\TeamMemberInterface;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamMemberRepository implements TeamMemberInterface
{
    public function getPaginatedFilteredTeamMembers(Request $request)
    {
        return Company::where('id', $request->currentCompany->id)->first()->users()->paginate()->appends(request()->query());
    }

    public function getTeamMemberById(Request $request, $member_id)
    {
        $user = User::findOrFail($member_id);
        if ($user->companies()->where('id', $request->currentCompany->id)->exists()) {
            return $user;
        }
        throw new \Exception('User not found');
    }

    public function newTeamMember(Request $request)
    {
        $member = new User();

        // Fill model with old input
        if (!empty($request->old())) {
            $member->fill($request->old());
        }

        return $member;
    }

    public function createTeamMember(Request $request)
    {
        $company = $request->currentCompany;

        // Create new Member
        $member = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Attach Member to Company
        $member->attachCompany($company);

        // Assign Member Role
        $member->assignRole('staff');
        $member->syncPermissions(is_array($request->permissions) ? array_keys($request->permissions) : []);

        if (!get_system_setting('verify_user_email_address')) {
            $member->update([
                'email_verified_at' => now(),
            ]);
        }

        // Upload and save avatar
        if ($request->avatar) {
            $request->validate(['avatar' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->avatar->storeAs('avatars', 'avatar-' . $member->id . '.' . $request->avatar->getClientOriginalExtension(), 'public_dir');
            $member->setSetting('avatar', asset('/uploads/' . $path));
        }

        return $member;
    }

    public function updateTeamMember(Request $request, $member_id)
    {
        $member = $this->getTeamMemberById($request, $member_id);

        // Update the Member
        $validated = $request->validated();
        unset($validated['password']);
        $member->update($validated);

        // Sync member role
        $member->syncPermissions(is_array($request->permissions) ? array_keys($request->permissions) : []);

        // If Password fields are filled
        if ($request->password) {
            $member->password = Hash::make($request->password);
            $member->save();
        }

        // Upload and save avatar
        if ($request->avatar) {
            $request->validate(['avatar' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->avatar->storeAs('avatars', 'avatar-' . $member->id . '.' . $request->avatar->getClientOriginalExtension(), 'public_dir');
            $member->setSetting('avatar', asset('/uploads/' . $path));
        }

        return $member;
    }

    public function deleteTeamMember(Request $request, $member_id)
    {
        $member = $this->getTeamMemberById($request, $member_id);

        return $member->delete();
    }
}
