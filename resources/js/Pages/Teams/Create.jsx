import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, useForm} from '@inertiajs/react';
import CreatableSelect from 'react-select/creatable';
import React, { useState } from 'react';

const components = {
    DropdownIndicator: null,
};

export default function Create() {

    const { data, setData, post, processing, reset, errors } = useForm({
        name: '',

    });

    const createOption = (label) => ({
        label,
        value: label,
    });

    const [inputValue, setInputValue] = useState('');
    const [value, setValue] = useState([]);
    const [highlightedValue, setHighlightedValue] = useState(null);
    const [inputError, setInputError] = useState(null);


    const customStyles = {
        multiValue: (styles, { data }) => {
            if (data.value === highlightedValue) {
                return {
                    ...styles,
                    backgroundColor: '#ffe4e6', // light red
                    border: '1px solid #f43f5e',
                };
            }
            return styles;
        },
    };


    const isValidEmail = (email) =>
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());




        const tryAddInputValue = () => {
            const normalizedInput = inputValue.trim().toLowerCase();
            const isDuplicate = value.some(opt => opt.value.toLowerCase() === normalizedInput);

            if (!normalizedInput) return;

            if (!isValidEmail(normalizedInput)) {
                setInputError('Please enter a valid email address.');
                setInputValue('');
                return false;
            }

            if (isDuplicate) {
                setInputValue('');
                return;
            }

            // ✅ Valid + Unique
            const newOption = createOption(normalizedInput);
            const updated = [...value, newOption];
            setValue(updated);
            setData('members', updated.map(opt => opt.value));
            setInputValue('');
            setInputError(null);
        };

        const handleKeyDown = (event) => {
            if (event.key === 'Enter' || event.key === 'Tab') {
                tryAddInputValue();
                event.preventDefault();
            }
        };






    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Create Team</h2>}
        >
            <Head title="Create Team" />

            <div className="py-12">

                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">

                    <div className="bg-white shadow-sm sm:rounded-lg p-6">


                    <form onSubmit={(e) => {
                        e.preventDefault();
                        post(route('teams.store'), {
                            preserveScroll: true,
                            onSuccess: () => reset(),
                        });

                    }}>
                        <div className="flex flex-col space-y-4">
                        <label className="flex flex-col space-y-1">
                            <span>New Team's Name:</span>
                            <input
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                type="text"
                                required
                                className="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Team Name"
                            />
                        </label>


                        <label className="flex flex-col space-y-1">
                            <span>Invite team members by email address:</span>
                            <CreatableSelect
                                components={components}
                                styles={customStyles}
                                inputValue={inputValue}
                                isClearable
                                isMulti
                                menuIsOpen={false}
                                onChange={(newValue) => {
                                    setValue(newValue);
                                }}
                                onInputChange={(newValue) => setInputValue(newValue)}
                                onBlur={tryAddInputValue}
                                onKeyDown={handleKeyDown}
                                placeholder="Enter email address and press enter"
                                value={value}
                            />
                            {inputError && (
                                <div className="mt-1 text-sm text-red-600">
                                    {inputError}
                                </div>
                            )}
                        </label>
                            <button
                                type="submit"
                                className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
                                disabled={processing}
                            >
                                Create Team
                            </button>
                        </div>

            </form>

                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    )
}
