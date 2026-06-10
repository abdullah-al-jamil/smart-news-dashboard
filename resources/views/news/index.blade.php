@extends('layouts.app')

@section('content')
<div x-data="newsApp()" x-init="init()">
    <h1 class="text-2xl font-bold mb-4">📰 {{ ucfirst($category) }} News</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="(article, idx) in articles" :key="idx">
            <div class="bg-white rounded shadow p-4">
                <img :src="article.urlToImage || 'https://via.placeholder.com/300'" class="w-full h-48 object-cover rounded">
                <h2 class="font-bold text-lg mt-2" x-text="article.title"></h2>
                <p class="text-gray-600 text-sm" x-text="article.description"></p>
                
                <!-- Buttons -->
                <div class="flex gap-2 mt-3">
                    <button @click="summarize(article)" class="bg-blue-500 text-white px-2 py-1 rounded text-sm">🤖 Summarize</button>
                    <button @click="openChat(article)" class="bg-green-500 text-white px-2 py-1 rounded text-sm">💬 Ask AI</button>
                    <button @click="bookmark(article)" class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">⭐ Save</button>
                </div>
                
                <!-- Summary display -->
                <div x-show="article.summary" x-html="article.summary" class="mt-2 text-sm bg-gray-100 p-2 rounded"></div>
            </div>
        </template>
    </div>

    <!-- Chat modal -->
    <div x-show="chatOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded p-6 w-96">
            <h3 class="font-bold">Ask about: <span x-text="currentArticle?.title"></span></h3>
            <input type="text" x-model="question" placeholder="Your question..." class="border p-2 w-full mt-2">
            <button @click="askAI()" class="bg-blue-500 text-white p-2 mt-2 w-full">Ask</button>
            <div x-show="answer" class="mt-3 p-2 bg-gray-100" x-text="answer"></div>
            <button @click="chatOpen=false" class="mt-3 text-red-500">Close</button>
        </div>
    </div>
</div>

<script>
function newsApp() {
    return {
        articles: @json($articles).map(a => ({ ...a, summary: '' })),
        chatOpen: false,
        currentArticle: null,
        question: '',
        answer: '',
        init() {
            console.log('News feed loaded');
        },
        async summarize(article) {
            console.log('article:', article);
            const response = await fetch('{{ route("ai.summarize") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ content: article.content || article.description })
            });
            const data = await response.json();
            article.summary = `<strong>Summary:</strong> ${data.summary}`;
            setTimeout(() => { article.summary = ''; }, 10000);
        },
        openChat(article) {
            this.currentArticle = article;
            this.chatOpen = true;
            this.question = '';
            this.answer = '';
        },
        async askAI() {
            const response = await fetch('{{ route("ai.chat") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({
                    article_content: this.currentArticle.content || this.currentArticle.description,
                    question: this.question
                })
            });
            const data = await response.json();
            this.answer = data.answer;
        },
        async bookmark(article) {
            const response = await fetch(`/bookmark/${article.url}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
            const data = await response.json();
            alert(data.bookmarked ? 'Saved' : 'Removed');
        }
    }
}
</script>
@endsection