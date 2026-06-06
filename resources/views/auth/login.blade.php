<x-layouts.app title="Login - Jagoan Bimbel">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .login-page {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .login-page [x-cloak] {
            display: none !important;
        }
    </style>

    <section
        x-data="loginPage()"
        x-init="redirectIfAuthenticated()"
        class="login-page flex min-h-screen items-center justify-center bg-[#F5F5F5] px-5 py-8 text-[#001A41] sm:px-8 lg:px-10"
    >
        <div class="w-full max-w-131.25">
            <div class="mb-9 text-center sm:mb-11">
                <h1 class="text-[30px] font-extrabold leading-tight sm:text-[38px]">
                    Selamat Datang Kembali
                </h1>
                <p class="mt-3 text-base font-medium text-[#17385F] sm:text-lg">
                    Masuk untuk melanjutkan progres belajarmu
                </p>
            </div>

            <div class="rounded-[26px] border border-[#DCE6F2] bg-white px-6 py-9 shadow-[0_18px_45px_rgba(21,45,75,0.08)] sm:px-10 sm:py-10 md:px-10">

                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="email" class="sr-only">Email</label>

                        <div class="relative">
                            <span class="pointer-events-none absolute left-5 top-1/2 flex -translate-y-1/2 text-[#8CA2C4]">
                                <svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 6.5h16v11H4v-11Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="m4.5 7 7.5 6 7.5-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <input
                                id="email"
                                type="email"
                                x-model="form.email"
                                autocomplete="email"
                                class="h-15 w-full rounded-[20px] border border-[#D9E4F1] bg-white pl-13.75 pr-5 text-lg font-medium text-[#001A41] outline-none transition placeholder:text-[#8CA2C4] focus:border-[#1591DC] focus:ring-4 focus:ring-[#1591DC]/10"
                                placeholder="Email kamu"
                                required
                            >
                        </div>

                        <p
                            x-show="errors.email"
                            x-cloak
                            x-text="errors.email"
                            class="mt-2 text-sm font-medium text-red-600"
                        ></p>
                    </div>

                    <div>
                        <label for="password" class="sr-only">Password</label>

                        <div class="relative">
                            <span class="pointer-events-none absolute left-5 top-1/2 flex -translate-y-1/2 text-[#8CA2C4]">
                                <svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                    <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M6 10h12v10H6V10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                x-model="form.password"
                                autocomplete="current-password"
                                class="h-15 w-full rounded-[20px] border border-[#D9E4F1] bg-white pl-13.75 pr-14.5 text-lg font-medium text-[#001A41] outline-none transition placeholder:text-[#8CA2C4] focus:border-[#1591DC] focus:ring-4 focus:ring-[#1591DC]/10"
                                placeholder="Password"
                                required
                            >

                            <button
                                type="button"
                                class="absolute right-5 top-1/2 flex -translate-y-1/2 text-[#8CA2C4] transition hover:text-[#2C5EAD] cursor-pointer"
                                :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                                @click="showPassword = !showPassword"
                            >
                                <svg x-show="!showPassword" aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <svg x-show="showPassword" x-cloak aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                    <path d="M2.5 12s3.5-6 9.5-6a9 9 0 0 1 6.7 3.1M21.5 12s-3.5 6-9.5 6a9 9 0 0 1-6.7-3.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.2 9.2A3 3 0 0 0 12 15a3 3 0 0 0 2.8-4.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M4 4l16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>

                        <p
                            x-show="errors.password"
                            x-cloak
                            x-text="errors.password"
                            class="mt-2 text-sm font-medium text-red-600"
                        ></p>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="mt-8 flex h-14.5 w-full items-center justify-center rounded-[20px] bg-[#1591DC] px-4 text-lg font-bold text-white shadow-[0_10px_20px_rgba(21,145,220,0.22)] transition hover:bg-[#2C5EAD] focus:outline-none focus:ring-4 focus:ring-[#1591DC]/20 disabled:cursor-not-allowed disabled:opacity-70 cursor-pointer"
                    >
                        <span x-show="!loading">Masuk</span>
                        <span x-show="loading" x-cloak>Memproses...</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        function loginPage() {
            return {
                loading: false,
                errorMessage: '',
                errors: {},
                showPassword: false,
                form: {
                    email: '',
                    password: '',
                },

                redirectIfAuthenticated() {
                    const token = localStorage.getItem('access_token');
                    const user = JSON.parse(localStorage.getItem('auth_user') || 'null');

                    if (!token || !user?.role) {
                        return;
                    }

                    window.location.href = user.role === 'admin'
                        ? '/admin/materi'
                        : '/siswa/materi';
                },

                async submit() {
                    this.loading = true;
                    this.errors = {};
                    this.errorMessage = '';

                    try {
                        const response = await fetch('/api/login', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(this.form),
                        });

                        const payload = await response.json();

                        if (!response.ok) {
                            this.handleFailedLogin(payload);
                            return;
                        }

                        localStorage.setItem('access_token', payload.data.access_token);
                        localStorage.setItem('token_type', payload.data.token_type);
                        localStorage.setItem('auth_user', JSON.stringify(payload.data.user));

                        window.location.href = payload.data.user.role === 'admin'
                            ? '/admin/materi'
                            : '/siswa/materi';
                    } catch (error) {
                        this.errorMessage = 'Terjadi kesalahan koneksi. Pastikan server Laravel berjalan.';
                    } finally {
                        this.loading = false;
                    }
                },

                handleFailedLogin(payload) {
                    const validationErrors = payload.errors || {};

                    this.errors = Object.fromEntries(
                        Object.entries(validationErrors).map(([field, messages]) => [
                            field,
                            Array.isArray(messages) ? messages[0] : messages,
                        ]),
                    );

                    this.errorMessage = payload.message || 'Login gagal. Periksa email dan password.';
                },
            };
        }
    </script>
</x-layouts.app>
