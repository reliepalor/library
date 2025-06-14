<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            @if (session('status') === 'profile-updated')
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ __('Profile updated successfully.') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Profile Picture -->
                <div>
                    <x-input-label for="profile_picture" :value="__('Profile Picture')" />
                    <input id="profile_picture" name="profile_picture" type="file" class="mt-1 block w-full" />
                    @error('profile_picture')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="mt-2 w-24 h-24 rounded-full object-cover">
                    @endif
                </div>

                <!-- Name -->
                <div class="mt-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    @error('name')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email" />
                    @error('email')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- College -->
                <div class="mt-4">
                    <x-input-label for="college" :value="__('College')" />
                    <x-text-input id="college" name="college" type="text" class="mt-1 block w-full" :value="old('college', $user->college)" />
                    @error('college')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year -->
                <div class="mt-4">
                    <x-input-label for="year" :value="__('Year')" />
                    <x-text-input id="year" name="year" type="text" class="mt-1 block w-full" :value="old('year', $user->year)" />
                    @error('year')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Other Info -->
                <div class="mt-4">
                    <x-input-label for="other_info" :value="__('Other Information')" />
                    <textarea id="other_info" name="other_info" class="mt-1 block w-full rounded-md border-gray-300" rows="3">{{ old('other_info', $user->other_info) }}</textarea>
                    @error('other_info')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>
                        {{ __('Save') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
