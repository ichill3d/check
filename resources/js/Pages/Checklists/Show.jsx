import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';


export default function Show({ checklist }) {
    const { data, setData, post, processing, reset, errors } = useForm({
        content: '',
    });

    const {
        data: titleForm,
        setData: setTitleData,
        patch,
        processing: titleProcessing,
        errors: titleErrors,
    } = useForm({
        title: checklist.title,
    });

    const [confirmingItemId, setConfirmingItemId] = useState(null);
    const [editChecklistTitle, setEditChecklistTitle] = useState(null);

    const handleDelete = (id) => {
        router.delete(route('checklists.items.destroy', id), {
            preserveScroll: true,
            onSuccess: () => setConfirmingItemId(null),
        });
    };

    const updateTitle = (e) => {
        e.preventDefault();
        patch(route('checklists.update', checklist.id), {
            preserveScroll: true,
            onSuccess: () => setEditChecklistTitle(null),
        });
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('checklists.items.store', checklist.id), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <AuthenticatedLayout
            // header={<h2 className="text-xl font-semibold leading-tight text-gray-800">{checklist.title}</h2>}
        >
            <Head title={checklist.title} />

            <div className="py-12">

                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">

                <div className="bg-white shadow-sm sm:rounded-lg p-6">




                    {editChecklistTitle === checklist.id ? (
                        <form onSubmit={updateTitle} className="flex flex-row space-x-2 justify-between items-center">
                            <input
                                type="text"
                                value={titleForm.title}
                                onChange={(e) => setTitleData('title', e.target.value)}
                                className="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {titleErrors.title && (
                                <div className="text-sm text-red-600 mt-1">{titleErrors.title}</div>
                            )}

                            <div className="flex items-center space-x-4">
                                <button
                                    type="submit"
                                    className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 whitespace-nowrap"
                                    disabled={titleProcessing}
                                >
                                    Update Title
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setEditChecklistTitle(null)}
                                    className="text-gray-600 bg-gray-300 px-4 py-2 rounded hover:text-gray-800 text-sm"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    ) : (
                        <div className="flex items-center justify-between space-x-2">
                            <h2 className="font-bold text-xl bg-indigo-100 p-2 rounded w-full">{checklist.title}</h2>
                            <button onClick={() => setEditChecklistTitle(checklist.id)}
                                    className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
                            >
                                Edit
                            </button>
                        </div>
                    )}

                    <div className="mt-6 border-t pt-4">
                        <form onSubmit={submit}>
                            <div className="mb-4">
                                <label
                                    htmlFor="content"
                                    className="block text-sm font-medium text-gray-700 w-full"
                                >
                                    Add New Item
                                </label>
                                <div className="flex flex-row space-x-2 items-center justify-between">

                                    <input
                                        type="text"
                                        id="content"
                                        name="content"
                                        value={data.content}
                                        onChange={(e) => setData('content', e.target.value)}
                                        required
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Enter item content"
                                    />

                                    <button
                                        type="submit"
                                        className="px-4 py-2 bg-indigo-500 text-white rounded shadow hover:bg-indigo-600 whitespace-nowrap"
                                        disabled={processing}
                                    >
                                        Add Item
                                    </button>

                                </div>
                            </div>
                            {errors.content && (
                                <div className="text-red-600 text-sm mt-1">{errors.content}</div>
                            )}


                        </form>
                    </div>

                    <ul className="space-y-2">
                        {checklist.items.map((item) => (
                            <li key={item.id} className="border p-4 rounded shadow flex items-center justify-between">


                                <form
                                    onSubmit={(e) => {
                                        e.preventDefault();
                                        router.post(route('checklists.items.toggle', item.id), {}, {
                                            preserveScroll: true,
                                        });
                                    }}
                                >
                                    <label className="flex flex-row space-x-2 items-center cursor-pointer">
                                        <button
                                            type="submit"
                                            className={`inline-flex items-center justify-center w-5 h-5 border-2 rounded ${
                                                item.is_done ? 'bg-green-500 border-green-500' : 'border-gray-400'
                                            }`}
                                            title="Toggle item"
                                        >
                                            {item.is_done ? (
                                                <span>✅</span>
                                            ) : (
                                                <span className="w-3 h-3 block bg-white rounded-full" />
                                            )}
                                        </button>
                                        <div className={`font-medium ${item.is_done ? 'line-through text-gray-400' : ''}`}>
                                            {item.content}
                                        </div>
                                    </label>
                                </form>




                                <div className="flex items-center ml-4">
                                    {/* ✅ Toggle Button or Confirmation */}
                                    {confirmingItemId === item.id ? (
                                        <div className="text-sm text-red-600 space-x-2 ml-2">
                                            <span>Delete Item?</span>
                                            <button
                                                type="button"
                                                onClick={() => handleDelete(item.id)}
                                                className="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                                            >
                                                Yes
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => setConfirmingItemId(null)}
                                                className="px-2 py-1 bg-gray-300 text-gray-800 text-xs rounded hover:bg-gray-400"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    ) : (
                                        <button
                                            onClick={() => setConfirmingItemId(item.id)}
                                            className="ml-2 text-red-600 hover:text-red-800 text-sm"
                                            title="Delete item"
                                        >
                                            ✖
                                        </button>
                                    )}
                                </div>

                            </li>
                        ))}
                    </ul>



                </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
