<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Statistics dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="10"> 
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">IoT Monitor Kvality Ovzdušia</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <span class="text-sm font-semibold text-gray-500 uppercase">CO₂ / Air Quality</span>
                <div class="text-3xl font-extrabold text-emerald-600 mt-2">
                    /* Kvalita ovzdušia */
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <span class="text-sm font-semibold text-gray-500 uppercase">Teplota</span>
                <div class="text-3xl font-extrabold text-blue-600 mt-2">
                    /* Teplota */
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <span class="text-sm font-semibold text-gray-500 uppercase">Vlhkosť</span>
                <div class="text-3xl font-extrabold text-indigo-600 mt-2">
                   /* Vlhkosť */
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 font-bold text-gray-700">Posledné merania</div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b">
                        <th class="px-6 py-3">Čas</th>
                        <th class="px-6 py-3">CO₂</th>
                        <th class="px-6 py-3">Teplota</th>
                        <th class="px-6 py-3">Vlhkosť</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>