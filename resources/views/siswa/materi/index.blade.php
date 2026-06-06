<x-layouts.app title="Daftar Soal - Jagoan Bimbel">
    <section x-data="siswaMateriIndex()" x-init="init()" class="min-h-screen bg-[#F5F5F5]">
        <x-dashboard.navbar role="siswa" dashboard-href="/siswa/materi" />

        <div class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10">
            <x-dashboard.header
                eyebrow="Dashboard Siswa"
                title="Daftar Soal"
                description="Pilih materi soal untuk dipelajari."
            />

            <div
                x-show="errorMessage"
                x-cloak
                x-transition
                class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
            >
                <span x-text="errorMessage"></span>
            </div>

            <div class="overflow-hidden rounded-2xl border border-[#DCE6F2] bg-white shadow-[0_18px_45px_rgba(21,45,75,0.08)]">
                <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 h-8 w-1.5 rounded-full bg-[#2C5EAD]"></span>
                        <div>
                            <h2 class="text-xl font-extrabold text-[#001A41]">Materi Tersedia</h2>
                            <p class="mt-1 text-sm font-medium text-slate-600">
                                Semua soal yang sudah siap untuk dipelajari.
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="fetchMateris(meta.current_page || 1)"
                        class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-[#BFD7EB] px-4 py-2 text-sm font-bold text-[#2C5EAD] transition hover:border-[#1591DC] hover:bg-[#4BB8FA]/10"
                    >
                        Refresh
                    </button>
                </div>

                <div x-show="loading" x-cloak class="px-5 py-8 text-sm font-medium text-slate-500 sm:px-7">
                    Memuat daftar soal...
                </div>

                <div x-show="!loading && materis.length === 0" x-cloak class="px-5 py-8 text-sm font-medium text-slate-500 sm:px-7">
                    Belum ada soal yang tersedia.
                </div>

                <div x-show="!loading && materis.length > 0" x-cloak class="grid gap-4 px-4 pb-5 sm:px-6 md:grid-cols-2 lg:grid-cols-3 lg:px-7 lg:pb-6">
                    <template x-for="materi in materis" :key="materi.id">
                        <article class="flex min-h-63.75 flex-col overflow-hidden rounded-2xl border border-[#DCE6F2] bg-[#F8FBFE] shadow-sm transition hover:border-[#4BB8FA] hover:shadow-[0_14px_30px_rgba(21,45,75,0.10)]">
                            <div class="flex flex-1 flex-col p-4 sm:p-5">
                                <div class="mb-4 flex items-start justify-between gap-3">
                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-extrabold uppercase"
                                        :class="badgeClass(materi.type)"
                                        x-text="materi.type"
                                    ></span>

                                    <span class="text-right text-xs font-bold text-slate-500" x-text="formatDate(materi.created_at)"></span>
                                </div>

                                <h3 class="text-lg font-extrabold leading-7 text-[#0F2747]" x-text="limitText(materi.title, 64)"></h3>

                                <p class="mt-2 flex-1 text-sm font-medium leading-6 text-slate-600" x-text="limitText(materi.description, 135)"></p>
                            </div>

                            <div class="border-t border-[#DCE6F2] bg-white/70 px-4 py-3 sm:px-5">
                                <a
                                    :href="`/siswa/materi/${materi.id}`"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-[#1591DC] px-4 py-3 text-sm font-bold text-white shadow-[0_10px_20px_rgba(21,145,220,0.18)] transition hover:bg-[#2C5EAD]"
                                >
                                    Lihat Detail
                                </a>
                            </div>
                        </article>
                    </template>
                </div>

                <div
                    x-show="meta.last_page > 1"
                    x-cloak
                    class="flex flex-col gap-3 border-t border-[#E5EDF6] px-5 py-4 text-sm font-semibold text-slate-600 sm:flex-row sm:items-center sm:justify-between sm:px-7"
                >
                    <button
                        type="button"
                        :disabled="meta.current_page <= 1"
                        @click="fetchMateris(meta.current_page - 1)"
                        class="rounded-xl border border-[#BFD7EB] px-4 py-2 text-[#2C5EAD] transition hover:bg-[#4BB8FA]/10 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Sebelumnya
                    </button>

                    <span>
                        Halaman <span x-text="meta.current_page"></span> dari <span x-text="meta.last_page"></span>
                    </span>

                    <button
                        type="button"
                        :disabled="meta.current_page >= meta.last_page"
                        @click="fetchMateris(meta.current_page + 1)"
                        class="rounded-xl border border-[#BFD7EB] px-4 py-2 text-[#2C5EAD] transition hover:bg-[#4BB8FA]/10 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Berikutnya
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        function siswaMateriIndex() {
            return {
                loading: false,
                materis: [],
                meta: {},
                errorMessage: '',

                init() {
                    this.guardSiswa();
                    this.fetchMateris();
                },

                guardSiswa() {
                    const token = localStorage.getItem('access_token');
                    const user = JSON.parse(localStorage.getItem('auth_user') || 'null');

                    if (!token || user?.role !== 'siswa') {
                        window.location.href = '/login';
                    }
                },

                authHeaders() {
                    return {
                        'Accept': 'application/json',
                        'Authorization': `${localStorage.getItem('token_type') || 'Bearer'} ${localStorage.getItem('access_token')}`,
                    };
                },

                async fetchMateris(page = 1) {
                    this.loading = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch(`/api/list-materi?page=${page}`, {
                            headers: this.authHeaders(),
                        });

                        const payload = await response.json();

                        if (!response.ok) {
                            this.showError(payload.message || 'Gagal mengambil daftar soal.');
                            return;
                        }

                        this.materis = payload.data;
                        this.meta = payload.meta;
                    } catch (error) {
                        this.showError('Gagal terhubung ke server.');
                    } finally {
                        this.loading = false;
                    }
                },

                async logout() {
                    await fetch('/api/logout', {
                        method: 'POST',
                        headers: this.authHeaders(),
                    });

                    localStorage.clear();
                    window.location.href = '/login';
                },

                badgeClass(type) {
                    return {
                        pdf: 'bg-red-500/10 text-red-600',
                        image: 'bg-[#4BB8FA]/15 text-[#2C5EAD]',
                        youtube: 'bg-[#1591DC]/10 text-[#1591DC]',
                    }[type] || 'bg-slate-100 text-slate-600';
                },

                formatDate(value) {
                    return new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                    }).format(new Date(value));
                },

                limitText(value, limit = 120) {
                    if (!value || value.length <= limit) {
                        return value || '';
                    }

                    return `${value.slice(0, limit)}...`;
                },

                showError(message) {
                    this.errorMessage = message;

                    if (!window.Swal) {
                        alert(message);
                        return;
                    }

                    window.Swal.fire({
                        title: 'Terjadi kesalahan',
                        text: message,
                        icon: 'error',
                        confirmButtonColor: '#1591DC',
                    });
                },
            };
        }
    </script>
</x-layouts.app>
