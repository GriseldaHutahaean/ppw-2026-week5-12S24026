<x-layout title="Login Pustakawan | SIPUS-Del">
    <div class="mx-auto max-w-md">
        <div class="mb-8 border-b border-border-warm pb-6">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brass">Area pustakawan</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-charcoal">Login</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.attempt') }}" method="POST" class="space-y-4 border border-border-warm bg-white p-6">
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-charcoal">Email</label>
                <input id="email" type="email" name="email" required
                       value="{{ old('email', request()->cookie('del_last_user_email')) }}"
                       class="w-full rounded-md border border-border-warm px-4 py-3 text-sm">
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-semibold text-charcoal">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full rounded-md border border-border-warm px-4 py-3 text-sm">
            </div>
            <label class="flex items-center gap-2 text-sm text-muted">
                <input type="checkbox" name="remember" value="1"> Ingat saya
            </label>
            <button type="submit" class="w-full rounded-md bg-forest px-4 py-3 text-sm font-semibold text-white hover:bg-forest/90">Masuk</button>
        </form>
    </div>
</x-layout>