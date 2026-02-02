<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-950 bg-gray-50">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        
        {{-- CARD CONTAINER --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
                
                {{-- HEADER LOGO / TITLE INSIDE CARD --}}
                <div class="mb-8">
                    <h1 class="text-center text-2xl font-extrabold text-gray-900">Laravel</h1>
                    <h2 class="mt-2 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                        Set your password
                    </h2>
                </div>
                
                {{-- GLOBAL ERROR ALERT --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                            </div>
                        </div>
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('invitation.store', $token) }}" method="POST">
                    @csrf

                    {{-- EMAIL FIELD (READ ONLY / DISABLED) --}}
                    {{-- Ini tambahan baru untuk menampilkan email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                            Email address
                        </label>
                        <div class="mt-2">
                            <input id="email" type="email" value="{{ $user->email }}" disabled
                                class="block w-full rounded-md border-0 px-3 py-2 text-gray-500 bg-gray-50 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6 cursor-not-allowed select-none">
                        </div>
                    </div>

                    {{-- PASSWORD FIELD --}}
                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                            Password<span class="text-red-600">*</span>
                        </label>
                        <div class="mt-2 relative">
                            <input id="password" name="password" type="password" required 
                                class="block w-full rounded-md border-0 px-3 py-2 pr-10 text-gray-900 shadow-sm ring-1 ring-inset {{ $errors->has('password') ? 'ring-red-300 focus:ring-red-600' : 'ring-gray-300 focus:ring-indigo-600' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6">
                            
                            {{-- Password visibility toggle --}}
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" 
                                onclick="togglePasswordVisibility('password')" tabindex="-1">
                                <svg id="password-eye-open" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                <svg id="password-eye-closed" class="h-5 w-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                    <path d="M15.171 13.576l1.414 1.414A10.016 10.016 0 0120 10c-1.274-4.057-5.064-7-9.542-7a9.958 9.958 0 00-2.117.252l1.6 1.6a4 4 0 015.528 5.528z"/>
                                </svg>
                            </button>
                            
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- CONFIRM PASSWORD FIELD --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                            Confirm password<span class="text-red-600">*</span>
                        </label>
                        <div class="mt-2 relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="block w-full rounded-md border-0 px-3 py-2 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            
                            {{-- Password visibility toggle --}}
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" 
                                onclick="togglePasswordVisibility('password_confirmation')" tabindex="-1">
                                <svg id="password_confirmation-eye-open" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                <svg id="password_confirmation-eye-closed" class="h-5 w-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                    <path d="M15.171 13.576l1.414 1.414A10.016 10.016 0 0120 10c-1.274-4.057-5.064-7-9.542-7a9.958 9.958 0 00-2.117.252l1.6 1.6a4 4 0 015.528 5.528z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div>
                        <button type="submit" 
                            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition duration-75">
                            Set password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeOpen = document.getElementById(fieldId + '-eye-open');
            const eyeClosed = document.getElementById(fieldId + '-eye-closed');
            
            if (field.type === 'password') {
                field.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                field.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>