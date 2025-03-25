import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link, router} from '@inertiajs/react';
import {useState} from "react";

export default function Index({ teams }) {

    const [confirmingTeamId, setConfirmingTeamId] = useState(null);

    const handleDelete = (id) => {
        router.delete(route('teams.destroy', id), {
            preserveScroll: true,
            onSuccess: () => setConfirmingTeamId(null),
        });
    };

    const TeamItem = ({ team }) => (
        <li className="flex items-center space-x-4 border p-4 rounded shadow" key={team.id}>
            <Link href={route('teams.show', { id: team.id })} className="w-full block font-bold">
                {team.name}
                <div className="text-sm text-gray-500 font-normal">
                    Created: {new Date(team.created_at).toLocaleString()}
                </div>
            </Link>
        </li>
    );


    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">My Teams</h2>}
        >
            <Head title="My Teams" />
            <div className="bg-white shadow-sm sm:rounded-lg p-6 flex flex-row gap-4 w-full justify-between ">
                <div className="w-full">
                    <h2 className="font-bold text-xl bg-indigo-100 p-2 rounded w-full mb-2">Teams I have created</h2>
                {teams.owned.length === 0 ? (
                    <p className="text-gray-600">You have no teams yet.</p>
                ) : (
                    <ul className="space-y-2">
                        {teams.owned.map(team => (
                            <TeamItem key={team.id} team={team} />
                        ))}
                    </ul>
                )}
                </div>
                <div className="w-full">
                    <h2 className="font-bold text-xl bg-indigo-100 p-2 rounded w-full mb-2">Teams I am a member of</h2>
                    {teams.member.length === 0 ? (
                        <p className="text-gray-600">You have no teams yet.</p>
                    ) : (
                        <ul className="space-y-2">
                            {teams.member.map(team => (
                                <TeamItem key={team.id} team={team} />
                            ))}
                        </ul>
                    )}
                </div>



            </div>
        </AuthenticatedLayout>
    )
}
