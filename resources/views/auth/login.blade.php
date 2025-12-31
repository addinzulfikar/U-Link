@extends('layouts.app')

@section('title', 'Login - U-LINK')

@section('chromeless')
@endsection

@section('bodyClass', 'bg-text-primary text-background-paper')

@section('content')
<div class="flex min-h-screen items-center justify-center px-6 py-14">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 !no-underline hover:!no-underline">
                <span class="text-body font-semibold tracking-tight text-background-paper">
                    <span class="text-primary">U</span>-LINK
                </span>
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-body-sm text-border" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-white/10 bg-white/5 shadow-xero">
            <div class="p-6 sm:p-7">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-h3 text-background-paper">Log in</h1>
                        <p class="mt-2 text-body-sm text-border">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="font-semibold !text-background-paper !no-underline hover:!no-underline hover:opacity-90">Daftar</a>
                        </p>
                    </div>
                </div>

                <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-caption font-semibold uppercase tracking-wide text-border">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            class="w-full rounded-full border border-white/10 bg-text-primary/40 px-4 py-3 text-body text-background-paper placeholder:text-border focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/25"
                            placeholder="Email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                        >
                        @error('email')
                            <p class="mt-2 text-body-sm text-border">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-caption font-semibold uppercase tracking-wide text-border">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="w-full rounded-full border border-white/10 bg-text-primary/40 px-4 py-3 text-body text-background-paper placeholder:text-border focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/25"
                            placeholder="Password"
                            autocomplete="current-password"
                        >
                        @error('password')
                            <p class="mt-2 text-body-sm text-border">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center gap-2 text-body-sm text-border">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-text-primary/30 text-primary focus:ring-primary/25">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="w-full rounded-full bg-primary px-4 py-3 text-body font-semibold text-white shadow-xero-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                        Continue
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-caption text-border">
            Dengan masuk, kamu menyetujui ketentuan layanan dan kebijakan privasi U-LINK.
        </p>
    </div>
</div>
@endsection
