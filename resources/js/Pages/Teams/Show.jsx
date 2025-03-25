import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';
import { usePage } from '@inertiajs/react';

import { Head, useForm, router } from '@inertiajs/react';



export default function Show({team, members, invitations, owner}) {
    const { auth } = usePage().props;
    const isOwner = auth.user.id === team.owner_id;

    const [confirmingMemberRemoveId, setConfirmingMemberRemoveId] = useState(null);

    const { data, setData, post, processing, errors } = useForm({
        invited_email: '',
    });

    const handleRemoveMember = (memberId) => {
        router.delete(route('teams.members.destroy', {
            teamId: team.id,
            memberId: memberId,
        }), {
            preserveScroll: true,
            onSuccess: () => setConfirmingMemberRemoveId(null),
        });
    };
    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Team Settings</h2>}
        >
            <Head title={team.name + ` - Team Settings`} />

            <div className="py-12">

                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">

                    <div className="bg-white shadow-sm sm:rounded-lg p-6">
                        <h2 className="font-bold text-xl bg-indigo-100 p-2 rounded w-full">{team.name}</h2>

                        {isOwner && (
                        <div className="bg-gray-100 p-2 rounded w-full mt-4">

                            {invitations.pending && invitations.pending.length > 0 && (
                                <div className="flex flex-col space-y-2 mb-4 border-b border-gray-300 py-2">
                                    <h3 className="text-gray-500 font-bold">Pending Invitations</h3>
                                    {invitations.pending.map((invitation) => (
                                        <div key={invitation.id}>
                                            {invitation.invited_email}
                                        </div>
                                    ))}
                                </div>
                            )}
                            <form onSubmit={(e) => {
                                e.preventDefault();
                                post(route('teams.invitations.store', team.id), {
                                    onSuccess: () => setData('invited_email', ''),
                                });
                            }}>
                                <div className="flex flex-col space-y-2 py-2">
                                    <h3 className="text-gray-500 font-bold">Invite members by email</h3>
                                <div className="flex flex-row space-x-2 mt-2 items-center justify-between">
                                    <input
                                        type="email"
                                        name="invited_email"
                                        placeholder="Email address"
                                        value={data.invited_email}
                                        onChange={(e) => setData('invited_email', e.target.value)}
                                        required
                                        className="border px-2 rounded w-full"
                                    />
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 whitespace-nowrap"
                                    >
                                        Invite Member
                                    </button>
                                </div>
                                {errors.invited_email && (
                                    <div className="text-sm text-red-600 mt-1">{errors.invited_email}</div>
                                )}
                                </div>


                            </form>
                        </div>
                            )}


                        <div className="mt-4  bg-gray-100 p-2 rounded w-full">
                            <h3 className="font-bold text-xl mb-4">Members</h3>
                            <div className="font-bold py-2">{owner.name} (Owner)</div>
                            {members && members.length > 0 ? (
                                <>

                                    {members.map((member) => (
                                        <div key={member.id} className="py-2 border-t border-gray-300 flex items-center space-x-4">
                                            <div>{member.name}</div>
                                            {isOwner && (
                                                <div className="text-sm text-gray-500 space-x-2">
                                                    {confirmingMemberRemoveId === member.id ? (
                                                        <div className="text-sm text-red-600 space-x-2 ml-2 whitespace-nowrap">
                                                            <span>Remove Member?</span>

                                                            <button
                                                                type="button"
                                                                onClick={() => handleRemoveMember(member.id)}
                                                                className="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                                                            >
                                                                Yes
                                                            </button>
                                                            <button
                                                                type="button"
                                                                onClick={() => setConfirmingMemberRemoveId(null)}
                                                                className="px-2 py-1 bg-gray-300 text-gray-800 text-xs rounded hover:bg-gray-400"
                                                            >
                                                                Cancel
                                                            </button>

                                                        </div>
                                                    ) : (
                                                    <button className="text-red-500 hover:underline"
                                                            onClick={() => setConfirmingMemberRemoveId(member.id)}
                                                    >Remove</button>
                                                        )}
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                </>
                            ) : (
                                <div>No members yet.</div>
                            )}
                        </div>

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
