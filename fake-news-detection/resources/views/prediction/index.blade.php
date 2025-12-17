<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fake News Detector</title>
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
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                    F
                </div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">Fake News Detector</h1>
            </div>
            <nav class="hidden md:flex gap-6 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}" class="text-indigo-600 font-semibold">New Prediction</a>
                <a href="{{ route('history') }}" class="hover:text-indigo-600 transition-colors">History</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-8">
            
            <!-- Intro -->
            <div class="text-center space-y-4">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Verify News Credibility
                </h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Paste the content of a news article below to analyze its authenticity using our advanced AI model.
                </p>
            </div>

            <!-- Error Message -->
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Prediction Result -->
            @if(isset($result))
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200 animate-fade-in-up">
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                            <div class="text-center sm:text-left">
                                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-1">Prediction Result</p>
                                @if($result['prediction'] === 'Real')
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 text-green-700 font-bold text-2xl">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        REAL NEWS
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-100 text-red-700 font-bold text-2xl">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        FAKE NEWS
                                    </div>
                                @endif
                            </div>

                            <div class="w-full sm:w-1/2">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm font-medium text-slate-700">Confidence Score</span>
                                    <span class="text-sm font-bold text-slate-900">{{ number_format($result['confidence_score'] * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-1000 ease-out {{ $result['prediction'] === 'Real' ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $result['confidence_score'] * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-slate-500 mt-2 text-right">Based on CNN Model Analysis</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100">
                        <p class="text-sm text-slate-600">
                            <span class="font-semibold">Analyzed Text Snippet:</span> 
                            "{{ \Illuminate\Support\Str::limit($news_text, 100) }}"
                        </p>
                    </div>
                </div>
            @endif

            <!-- Input Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                <form action="{{ route('predict') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <label for="news_text" class="block text-sm font-medium text-slate-700">
                            News Article Text
                        </label>
                        <div class="relative">
                            <textarea 
                                id="news_text" 
                                name="news_text" 
                                rows="8" 
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-4 bg-slate-50 placeholder-slate-400 transition-all"
                                placeholder="Paste the full text of the news article here..."
                                required
                            >{{ old('news_text', $news_text ?? '') }}</textarea>
                            <div class="absolute bottom-3 right-3 text-xs text-slate-400">
                                Min 10 chars
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-[1.01] active:scale-[0.99]">
                            Analyze Authenticity
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Predictions -->
            @if(isset($recentPredictions) && $recentPredictions->count() > 0)
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900">Recent Predictions</h3>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <ul class="divide-y divide-slate-100">
                            @foreach($recentPredictions as $history)
                                <li class="hover:bg-slate-50 transition-colors">
                                    <a href="{{ route('history', $history->id) }}" class="block p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0 pr-4">
                                                <p class="text-sm text-slate-900 truncate">
                                                    {{ \Illuminate\Support\Str::limit($history->news_text, 80) }}
                                                </p>
                                                <p class="text-xs text-slate-500 mt-1">
                                                    {{ $history->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $history->prediction === 'Real' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $history->prediction }}
                                                </span>
                                                <span class="text-xs font-semibold text-slate-600">
                                                    {{ number_format($history->confidence_score * 100, 0) }}%
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-slate-400">
                &copy; {{ date('Y') }} Fake News Detector. Powered by TensorFlow & Laravel.
            </p>
        </div>
    </footer>

</body>
</html>
