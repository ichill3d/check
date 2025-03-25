import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link, router} from '@inertiajs/react';
import {useState} from "react";

export default function Index({ checklists }) {

    const [confirmingChecklistId, setConfirmingChecklistId] = useState(null);

    const handleDelete = (id) => {
        router.delete(route('checklists.destroy', id), {
            preserveScroll: true,
            onSuccess: () => setConfirmingChecklistId(null),
        });
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">My Checklists</h2>}
        >
            <Head title="My Checklists" />

            <div className="py-12">

                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-6">
                        <Link
                            href={route('checklists.create')}
                            className="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
                        >
                            Create New Checklist
                        </Link>
                    </div>
                    <div className="bg-white shadow-sm sm:rounded-lg p-6">
                        {checklists.length === 0 ? (
                            <p className="text-gray-600">You have no checklists yet.</p>
                        ) : (
                            <ul className="space-y-2">
                                {checklists.map((checklist) => (
                                    <li key={checklist.id} className="flex items-center space-x-4 border p-4 rounded shadow">
                                        <Link href={route('checklists.show', {id: checklist.id})}
                                              className="w-full  block font-bold">{checklist.title}
                                            <div className="text-sm text-gray-500 font-normal">
                                                Created: {new Date(checklist.created_at).toLocaleString()}
                                            </div>
                                        </Link>
                                        {confirmingChecklistId === checklist.id ? (
                                            <div className="text-sm text-red-600 space-x-2 ml-2 whitespace-nowrap">
                                                <span>Delete Checklist?</span>

                                                <button
                                                    type="button"
                                                    onClick={() => handleDelete(checklist.id)}
                                                    className="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                                                >
                                                    Yes
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => setConfirmingChecklistId(null)}
                                                    className="px-2 py-1 bg-gray-300 text-gray-800 text-xs rounded hover:bg-gray-400"
                                                >
                                                    Cancel
                                                </button>

                                            </div>
                                        ) : (
                                            <button
                                                onClick={() => setConfirmingChecklistId(checklist.id)}
                                                className="ml-2 text-red-600 hover:text-red-800 text-sm"
                                                title="Delete item"
                                            >
                                                ✖
                                            </button>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        )}


                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
