import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';


export default function Settings({ notificationPrefs, notificationTypes, notificationChannels }) {

    const handleToggleNotification = (id) => {
        router.patch(route('notifications.settings.toggle', id), {
            preserveScroll: true
        });
    };

    return (

        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Notification Settings
                </h2>
            }
        >
            <Head title="Notification Settings" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        {notificationTypes.map(type => (
                            <div className="flex flex-col  py-4 border-b border-gray-200" key={type.key}>
                                <h3 className="text-lg font-medium text-gray-900 pb-2">{type.label}</h3>
                                <div className="flex flex-row space-x-4">
                                    {notificationChannels.map(channel => {
                                        const prefKey = `${type.key}-${channel.key}`;
                                        const pref = notificationPrefs[prefKey];

                                        return (
                                            <label key={channel.key} className="flex flex-row space-x-2 items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={pref?.enabled ?? false}
                                                    onChange={() => handleToggleNotification(pref.id)}
                                                />
                                                <span>{channel.label}</span>
                                            </label>
                                        );
                                    })}

                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
