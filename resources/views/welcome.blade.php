<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project & Client Billing Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#f0f0f0] text-[#2d3748] antialiased">
    <div class="flex flex-col items-center justify-center min-h-screen px-6 py-16">
        
        <header class="max-w-4xl text-center mb-16">
            <h1 class="text-5xl font-extrabold tracking-tight text-[#000000] mb-6 leading-tight">
                Client Sync <span class="text-gray-500"> Project & Client Billing Management</span>
            </h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto">
                Manage workflows and collaborate effectively to track project progress and financial transparency in real-time.
            </p>
        </header>

        <div class="flex flex-col items-center gap-6">
            <a href="{{ url('/portal') }}" 
               class="px-12 py-5 bg-gray-900 hover:bg-gray-600 text-white rounded-2xl font-bold text-lg transition-all shadow-lg shadow-gray-200 hover:-translate-y-1">
                Access Portal
            </a>
        </div>

    </div>
</body>
</html>