<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Management Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#f0f0f0] text-[#2d3748] antialiased">
    <div class="flex flex-col items-center justify-center min-h-screen px-6 py-16">
        
        <header class="max-w-4xl text-center mb-16">
            <h1 class="text-5xl font-extrabold tracking-tight text-[#000000] mb-6 leading-tight">
                Project & Billing <span class="text-gray-500">Management System</span>
            </h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto">
                Manage workflows and collaborate effectively to track project progress and financial transparency in real-time.
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl w-full mb-16">
            <div class="group p-8 bg-white rounded-3xl border-2 border-transparent hover:border-gray-200 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="w-12 h-12 bg-gray-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Dev Workflow</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Streamline development tasks, track sprint progress, and manage repository integration for efficient delivery.</p>
            </div>

            <div class="group p-8 bg-white rounded-3xl border-2 border-transparent hover:border-gray-200 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="w-12 h-12 bg-gray-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Client Portal</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Dedicated space for clients to view project milestones, share feedback, and maintain clear communication.</p>
            </div>

            <div class="group p-8 bg-white rounded-3xl border-2 border-transparent hover:border-gray-200 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="w-12 h-12 bg-gray-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Billing System</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Automated invoicing and payment tracking. High transparency on budget allocation and project expenses.</p>
            </div>
        </div>

        <div class="flex flex-col items-center gap-6">
            <a href="{{ url('/portal') }}" 
               class="px-12 py-5 bg-gray-900 hover:bg-gray-600 text-white rounded-2xl font-bold text-lg transition-all shadow-lg shadow-gray-200 hover:-translate-y-1">
                Access Portal
            </a>
        </div>

    </div>
</body>
</html>