<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Welcome, {{ $user->name }}</h2>
        <p class="mb-4 text-gray-600 text-center">Please set a new password to access your account.</p>

        <form action="{{ route('invitation.store', $token) }}" method="POST">
            @csrf
            
            {{-- PASSWORD INPUT --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                <input type="password" name="password" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror">
                
                {{-- Show specific error for password (e.g. "The password must be at least 8 characters") --}}
                @error('password')
                    <p class="text-red-500 text-s italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- CONFIRM PASSWORD INPUT --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150">
                Save & Login
            </button>
        </form>
    </div>
</body>
</html>