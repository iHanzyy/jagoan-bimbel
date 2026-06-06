<x-layouts.app title="Admin Materi - Jagoan Bimbel">
    <section x-data="adminMateriIndex()" x-init="init()" class="min-h-screen bg-[#F5F5F5]">
        <x-dashboard.navbar role="admin" dashboard-href="/admin/materi" />

        <div class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10">
            <x-dashboard.header
                eyebrow="Dashboard Admin"
                title="Manajemen Soal"
                description="Kelola soal PDF, gambar, dan link Youtube untuk siswa."
            >
                <x-slot:actions>
                    <a href="/admin/materi/create"
                        class="inline-flex items-center justify-center rounded-xl bg-[#1591DC] px-5 py-3 text-sm font-bold text-white shadow-[0_10px_20px_rgba(21,145,220,0.20)] transition hover:bg-[#2C5EAD]">
                        Upload Soal
                    </a>
                </x-slot:actions>
            </x-dashboard.header>

            <div
                class="overflow-hidden rounded-2xl border border-[#DCE6F2] bg-white shadow-[0_18px_45px_rgba(21,45,75,0.08)]">
                <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                    <div class="flex items-center gap-3">
                        <span class="h-8 w-1.5 rounded-full bg-[#2C5EAD]"></span>
                        <h2 class="text-xl font-extrabold text-[#001A41]">Daftar Soal</h2>
                    </div>

                    <button type="button" @click="fetchMateris(meta.current_page || 1)"
                        class="inline-flex items-center justify-center rounded-xl border border-[#BFD7EB] px-4 py-2 text-sm font-bold text-[#2C5EAD] transition hover:border-[#1591DC] hover:bg-[#4BB8FA]/10 cursor-pointer">
                        Refresh
                    </button>
                </div>

                <div x-show="loading" x-cloak class="px-5 py-8 text-sm font-medium text-slate-500 sm:px-7">
                    Memuat data...
                </div>

                <div x-show="!loading && materis.length === 0" x-cloak
                    class="px-5 py-8 text-sm font-medium text-slate-500 sm:px-7">
                    Belum ada materi.
                </div>

                <div x-show="!loading && materis.length > 0" x-cloak class="space-y-4 px-4 pb-5 sm:px-6 lg:px-7 lg:pb-6">
                    <div class="grid gap-4 md:grid-cols-2 lg:hidden">
                        <template x-for="materi in materis" :key="`card-${materi.id}`">
                            <article class="overflow-hidden rounded-2xl border border-[#DCE6F2] bg-[#F8FBFE] shadow-sm">
                                <div class="p-4 sm:p-5">
                                    <div class="mb-3 flex items-start justify-between gap-3">
                                        <span class="rounded-lg bg-[#4BB8FA]/15 px-3 py-1.5 text-xs font-extrabold uppercase text-[#2C5EAD]" x-text="materi.type"></span>
                                        <span class="text-right text-xs font-bold text-slate-500" x-text="formatDate(materi.created_at)"></span>
                                    </div>

                                    <h3 class="text-base font-extrabold leading-6 text-[#0F2747]" x-text="limitText(materi.title, 58)"></h3>
                                    <p class="mt-2 text-sm font-medium leading-6 text-slate-600" x-text="limitText(materi.description, 110)"></p>
                                </div>

                                <div class="flex items-center justify-end gap-2 border-t border-[#DCE6F2] bg-white/70 px-4 py-3 sm:px-5">
                                    <a :href="`/admin/materi/${materi.id}/edit`"
                                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#2C5EAD] text-white shadow-sm transition hover:bg-[#1591DC]"
                                        aria-label="Edit materi" title="Edit">
                                        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>

                                    <button x-show="materi.type === 'pdf' || materi.type === 'image'"
                                        type="button" @click="downloadMateri(materi)"
                                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#1591DC] text-white shadow-sm transition hover:bg-[#2C5EAD] cursor-pointer"
                                        aria-label="Download materi" title="Download">
                                        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 3v12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 21h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </button>

                                    <button x-show="materi.type === 'youtube'" type="button"
                                        @click="copyYoutubeLink(materi)"
                                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#1591DC] text-white shadow-sm transition hover:bg-[#2C5EAD] cursor-pointer"
                                        aria-label="Copy link Youtube" title="Copy Link">
                                        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M10 13a5 5 0 0 0 7.1.1l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14 11a5 5 0 0 0-7.1-.1l-2 2a5 5 0 0 0 7.1 7.1l1.1-1.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>

                                    <button type="button" @click="deleteMateri(materi)"
                                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-red-600 text-white shadow-sm transition hover:bg-red-700 cursor-pointer"
                                        aria-label="Hapus materi" title="Delete">
                                        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M10 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M6 7l1 14h10l1-14" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            <path d="M9 7V4h6v3" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                            </article>
                        </template>
                    </div>

                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-210 w-full overflow-hidden rounded-xl">
                            <thead class="bg-[#2C5EAD]">
                                <tr>
                                    <th class="px-5 py-4 text-left text-sm font-extrabold text-white">Judul</th>
                                    <th class="px-5 py-4 text-left text-sm font-extrabold text-white">Tipe</th>
                                    <th class="px-5 py-4 text-left text-sm font-extrabold text-white">Tanggal Upload</th>
                                    <th class="px-5 py-4 text-right text-sm font-extrabold text-white">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[#E5EDF6]">
                                <template x-for="materi in materis" :key="materi.id">
                                    <tr class="transition hover:bg-[#4BB8FA]/8">
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-[#0F2747]" x-text="limitText(materi.title, 50)"></p>
                                            <p class="mt-1 text-sm font-medium text-slate-500"
                                                x-text="limitText(materi.description, 80)"></p>
                                        </td>

                                        <td class="px-5 py-4">
                                            <span
                                                class="rounded-lg bg-[#4BB8FA]/15 px-3 py-1.5 text-xs font-extrabold uppercase text-[#2C5EAD]"
                                                x-text="materi.type"></span>
                                        </td>

                                        <td class="px-5 py-4 text-sm font-medium text-slate-600"
                                            x-text="formatDate(materi.created_at)"></td>

                                        <td class="px-5 py-4">
                                            <div class="flex flex-wrap justify-end gap-2">
                                            <a :href="`/admin/materi/${materi.id}/edit`"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-[#2C5EAD] text-white shadow-sm transition hover:bg-[#1591DC]"
                                                aria-label="Edit materi" title="Edit">
                                                <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </a>

                                            <button x-show="materi.type === 'pdf' || materi.type === 'image'"
                                                type="button" @click="downloadMateri(materi)"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-[#1591DC] text-white shadow-sm transition hover:bg-[#2C5EAD] cursor-pointer"
                                                aria-label="Download materi" title="Download">
                                                <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 3v12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M5 21h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </button>

                                            <button x-show="materi.type === 'youtube'" type="button"
                                                @click="copyYoutubeLink(materi)"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-[#1591DC] text-white shadow-sm transition hover:bg-[#2C5EAD] cursor-pointer"
                                                aria-label="Copy link Youtube" title="Copy Link">
                                                <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M10 13a5 5 0 0 0 7.1.1l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14 11a5 5 0 0 0-7.1-.1l-2 2a5 5 0 0 0 7.1 7.1l1.1-1.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>

                                            <button type="button" @click="deleteMateri(materi)"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-red-600 text-white shadow-sm transition hover:bg-red-700 cursor-pointer"
                                                aria-label="Hapus materi" title="Delete">
                                                <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M10 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M6 7l1 14h10l1-14" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                    <path d="M9 7V4h6v3" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="meta.last_page > 1" x-cloak
                    class="flex flex-col gap-3 border-t border-[#E5EDF6] px-5 py-4 text-sm font-semibold text-slate-600 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                    <button type="button" :disabled="meta.current_page <= 1"
                        @click="fetchMateris(meta.current_page - 1)"
                        class="rounded-xl border border-[#BFD7EB] px-4 py-2 text-[#2C5EAD] transition hover:bg-[#4BB8FA]/10 disabled:cursor-not-allowed disabled:opacity-50">
                        Sebelumnya
                    </button>

                    <span>
                        Halaman <span x-text="meta.current_page"></span> dari <span x-text="meta.last_page"></span>
                    </span>

                    <button type="button" :disabled="meta.current_page >= meta.last_page"
                        @click="fetchMateris(meta.current_page + 1)"
                        class="rounded-xl border border-[#BFD7EB] px-4 py-2 text-[#2C5EAD] transition hover:bg-[#4BB8FA]/10 disabled:cursor-not-allowed disabled:opacity-50">
                        Berikutnya
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        function adminMateriIndex() {
            return {
                loading: false,
                materis: [],
                meta: {},
                message: '',
                errorMessage: '',

                init() {
                    this.guardAdmin();
                    this.fetchMateris();
                },

                guardAdmin() {
                    const token = localStorage.getItem('access_token');
                    const user = JSON.parse(localStorage.getItem('auth_user') || 'null');

                    if (!token || user?.role !== 'admin') {
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
                            this.showError(payload.message || 'Gagal mengambil daftar materi.');
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

                async downloadMateri(materi) {
                    this.errorMessage = '';

                    try {
                        const response = await fetch(`/api/materi-download/${materi.id}`, {
                            headers: this.authHeaders(),
                        });

                        if (!response.ok) {
                            this.showError('Gagal mengunduh file.');
                            return;
                        }

                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');

                        link.href = url;
                        link.download = `${materi.title}.${materi.type === 'pdf' ? 'pdf' : 'file'}`;
                        document.body.appendChild(link);
                        link.click();
                        link.remove();

                        window.URL.revokeObjectURL(url);
                    } catch (error) {
                        this.showError('Gagal mengunduh file.');
                    }
                },

                async copyYoutubeLink(materi) {
                    try {
                        await navigator.clipboard.writeText(materi.youtube_url);
                        this.message = 'Link Youtube berhasil disalin.';
                        setTimeout(() => this.message = '', 2500);
                    } catch (error) {
                        this.showError('Gagal menyalin link Youtube.');
                    }
                },

                async deleteMateri(materi) {
                    const result = await this.confirmDelete(materi);

                    if (!result.isConfirmed) {
                        return;
                    }

                    this.errorMessage = '';

                    try {
                        const response = await fetch(`/api/delete-materi/${materi.id}`, {
                            method: 'DELETE',
                            headers: this.authHeaders(),
                        });

                        const payload = await response.json();

                        if (!response.ok) {
                            this.showError(payload.message || 'Gagal menghapus materi.');
                            return;
                        }

                        this.message = payload.message;
                        this.showSuccess('Berhasil dihapus!', payload.message || 'Materi berhasil dihapus.');
                        await this.fetchMateris(this.meta.current_page || 1);
                    } catch (error) {
                        this.showError('Gagal menghapus materi.');
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

                formatDate(value) {
                    return new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    }).format(new Date(value));
                },

                limitText(value, limit = 80) {
                    if (!value || value.length <= limit) {
                        return value || '';
                    }

                    return `${value.slice(0, limit)}...`;
                },

                confirmDelete(materi) {
                    if (!window.Swal) {
                        return Promise.resolve({
                            isConfirmed: confirm(`Hapus materi "${materi.title}"?`),
                        });
                    }

                    return window.Swal.fire({
                        title: 'Hapus materi?',
                        text: `Materi "${this.limitText(materi.title, 60)}" tidak bisa dikembalikan setelah dihapus.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1591DC',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                    });
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

                showSuccess(title, message) {
                    if (!window.Swal) {
                        return;
                    }

                    window.Swal.fire({
                        title,
                        text: message,
                        icon: 'success',
                        confirmButtonColor: '#1591DC',
                    });
                },
            };
        }
    </script>
</x-layouts.app>
