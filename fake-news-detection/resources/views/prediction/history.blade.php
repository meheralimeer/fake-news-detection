<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History - Fake News Detector</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-lg group-hover:bg-indigo-700 transition-colors">
                        F
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">Fake News Detector</h1>
                </a>
            </div>
            <nav class="flex gap-6 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">New Prediction</a>
                <a href="{{ route('history') }}" class="text-indigo-600 font-semibold">History</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto h-[calc(100vh-10rem)]">
            
            <div class="flex h-full gap-6">
                
                <!-- Sidebar / List -->
                <div class="{{ $selectedPrediction ? 'w-1/3 hidden md:block' : 'w-full max-w-4xl mx-auto' }} bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="p-4 border-b border-slate-100 bg-slate-50">
                        <h2 class="font-semibold text-slate-900">Prediction History</h2>
                        <p class="text-xs text-slate-500">{{ $history->count() }} records found</p>
                    </div>
                    <div class="overflow-y-auto flex-1 p-2 space-y-2">
                        @forelse($history as $item)
                            <a href="{{ route('history', $item->id) }}" class="block p-4 rounded-xl border transition-all {{ isset($selectedPrediction) && $selectedPrediction->id === $item->id ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' : 'border-slate-100 hover:border-indigo-200 hover:bg-slate-50' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->prediction === 'Real' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->prediction }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $item->created_at->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-sm text-slate-700 line-clamp-2">{{ $item->news_text }}</p>
                            </a>
                        @empty
                            <div class="p-8 text-center text-slate-500">
                                No history found. <a href="{{ route('home') }}" class="text-indigo-600 hover:underline">Make a prediction</a>.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Detail View -->
                @if($selectedPrediction)
                    <div class="w-full md:w-2/3 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col animate-fade-in">
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Prediction Details</h2>
                                <p class="text-sm text-slate-500">ID: #{{ $selectedPrediction->id }}</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs text-slate-500 uppercase tracking-wider">Result</span>
                                <span class="text-xl font-bold {{ $selectedPrediction->prediction === 'Real' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $selectedPrediction->prediction }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6 overflow-y-auto flex-1">
                            <!-- Confidence Bar -->
                            <div class="mb-8">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm font-medium text-slate-700">Confidence Score</span>
                                    <span class="text-sm font-bold text-slate-900">{{ number_format($selectedPrediction->confidence_score * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-3">
                                    <div class="h-3 rounded-full {{ $selectedPrediction->prediction === 'Real' ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $selectedPrediction->confidence_score * 100 }}%"></div>
                                </div>
                            </div>

                            <!-- Text Content -->
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-3">Analyzed Text</h3>
                                <div class="prose prose-slate max-w-none bg-slate-50 p-6 rounded-xl border border-slate-100 text-slate-800 leading-relaxed">
                                    {{ $selectedPrediction->news_text }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-slate-50 border-t border-slate-100 text-right">
                            <span class="text-xs text-slate-400">Analyzed on {{ $selectedPrediction->created_at->toDayDateTimeString() }}</span>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </main>

</body>
</html>
