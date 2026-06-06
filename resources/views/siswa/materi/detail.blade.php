<x-layouts.app title="Detail Soal - Jagoan Bimbel">
    <section x-data="siswaMateriDetail({{ $materiId }})" x-init="init()" class="min-h-screen bg-[#F5F5F5]">
        <x-dashboard.navbar role="siswa" dashboard-href="/siswa/materi" />

        <div class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10">
            <a href="/siswa/materi" class="mb-4 inline-flex items-center text-sm font-bold text-[#1591DC] transition hover:text-[#2C5EAD]">
                &larr; Kembali ke daftar soal
            </a>

            <x-dashboard.header
                eyebrow="Dashboard Siswa"
                title="Detail Soal"
                description="Pelajari materi berikut dengan sungguh-sungguh."
            />

            <div
                x-show="errorMessage"
                x-cloak
                x-transition
                class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
            >
                <span x-text="errorMessage"></span>
            </div>

            <div
                x-show="loading"
                x-cloak
                class="rounded-2xl border border-[#DCE6F2] bg-white p-6 text-sm font-semibold text-slate-500 shadow-[0_18px_45px_rgba(21,45,75,0.08)]"
            >
                Memuat detail soal...
            </div>

            <article
                x-show="!loading && materi"
                x-cloak
                class="overflow-hidden rounded-2xl border border-[#DCE6F2] bg-white shadow-[0_18px_45px_rgba(21,45,75,0.08)]"
            >
                <header class="border-b border-[#E5EDF6] px-5 py-6 sm:px-7">
                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        <span
                            class="rounded-lg px-3 py-1.5 text-xs font-extrabold uppercase"
                            :class="badgeClass(materi?.type)"
                            x-text="materi?.type"
                        ></span>

                        <span class="text-sm font-bold text-slate-500" x-text="formatDate(materi?.created_at)"></span>
                    </div>

                    <h1 class="text-2xl font-extrabold leading-tight text-[#001A41] sm:text-3xl lg:text-4xl" x-text="materi?.title"></h1>

                    <p class="mt-3 max-w-4xl text-sm font-medium leading-7 text-slate-600 sm:text-base" x-text="materi?.description"></p>
                </header>

                <div class="px-4 py-5 sm:px-6 lg:px-7">
                    <template x-if="materi?.type === 'pdf'">
                        <div class="space-y-4">
                            <div class="flex flex-col gap-3 rounded-2xl border border-[#DCE6F2] bg-[#F8FBFE] p-4 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-sm font-semibold leading-6 text-slate-600">
                                    Preview PDF ditampilkan di bawah. Gunakan tombol aksi kalau browser tidak menampilkan preview.
                                </p>

                                <div class="flex items-center gap-2">
                                    <a
                                        :href="materi.file_url"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#2C5EAD] text-white shadow-sm transition hover:bg-[#1591DC]"
                                        aria-label="Buka PDF di tab baru"
                                        title="Buka di tab baru"
                                    >
                                        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M14 4h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10 14 20 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M20 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>

                                    <a
                                        :href="materi.file_url"
                                        :download="downloadName()"
                                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#1591DC] text-white shadow-sm transition hover:bg-[#2C5EAD]"
                                        aria-label="Download PDF"
                                        title="Download"
                                    >
                                        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 3v12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 21h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <iframe
                                :src="materi.file_url"
                                x-on:load="pdfLoaded = true"
                                x-on:error="pdfFailed = true"
                                class="h-[68vh] min-h-105 w-full rounded-2xl border border-[#DCE6F2] bg-white"
                                title="Preview PDF"
                            ></iframe>

                            <div
                                x-show="pdfFailed"
                                x-cloak
                                class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                            >
                                Preview PDF gagal dimuat. Buka PDF di tab baru atau download file untuk tetap belajar.
                            </div>
                        </div>
                    </template>

                    <template x-if="materi?.type === 'image'">
                        <div class="space-y-4">
                            <div class="flex items-center justify-end gap-2 rounded-2xl border border-[#DCE6F2] bg-[#F8FBFE] p-4">
                                <a
                                    :href="materi.file_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#2C5EAD] text-white shadow-sm transition hover:bg-[#1591DC]"
                                    aria-label="Buka gambar di tab baru"
                                    title="Buka gambar"
                                >
                                    <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M14 4h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 14 20 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M20 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>

                                <a
                                    :href="materi.file_url"
                                    :download="downloadName()"
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#1591DC] text-white shadow-sm transition hover:bg-[#2C5EAD]"
                                    aria-label="Download gambar"
                                    title="Download"
                                >
                                    <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3v12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5 21h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="rounded-2xl border border-[#DCE6F2] bg-[#F8FBFE] p-3 sm:p-4">
                                <img
                                    x-show="!imageFailed"
                                    :src="materi.file_url"
                                    :alt="materi.title"
                                    x-on:load="imageLoaded = true"
                                    x-on:error="imageFailed = true"
                                    class="mx-auto max-h-[72vh] w-full rounded-xl object-contain"
                                >

                                <div
                                    x-show="imageFailed"
                                    x-cloak
                                    class="rounded-2xl border border-red-200 bg-red-50 px-4 py-6 text-center text-sm font-semibold text-red-700"
                                >
                                    Preview gambar gagal dimuat. Buka gambar di tab baru atau download file untuk melihat materi.
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="materi?.type === 'youtube'">
                        <div class="space-y-4">
                            <div class="aspect-video overflow-hidden rounded-2xl border border-[#DCE6F2] bg-black">
                                <iframe
                                    :src="materi.youtube_embed_url"
                                    x-on:error="youtubeFailed = true"
                                    class="h-full w-full"
                                    title="Preview Youtube"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>
                            </div>

                            <div
                                x-show="youtubeFailed"
                                x-cloak
                                class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                            >
                                Preview YouTube gagal dimuat. Buka video di YouTube untuk menonton materi.
                            </div>
                        </div>
                    </template>
                </div>
            </article>
        </div>
    </section>

    <script>
        function siswaMateriDetail(materiId) {
            return {
                materiId,
                loading: true,
                materi: null,
                errorMessage: '',
                pdfLoaded: false,
                pdfFailed: false,
                imageLoaded: false,
                imageFailed: false,
                youtubeFailed: false,

                init() {
                    this.guardSiswa();
                    this.fetchDetail();
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

                async fetchDetail() {
                    this.loading = true;
                    this.errorMessage = '';
                    this.resetPreviewState();

                    try {
                        const response = await fetch(`/api/detail-materi/${this.materiId}`, {
                            headers: this.authHeaders(),
                        });

                        const payload = await response.json();

                        if (!response.ok) {
                            this.showError(payload.message || 'Gagal mengambil detail soal.');
                            return;
                        }

                        this.materi = payload.data;
                    } catch (error) {
                        this.showError('Gagal terhubung ke server.');
                    } finally {
                        this.loading = false;
                    }
                },

                badgeClass(type) {
                    return {
                        pdf: 'bg-red-500/10 text-red-600',
                        image: 'bg-[#4BB8FA]/15 text-[#2C5EAD]',
                        youtube: 'bg-[#1591DC]/10 text-[#1591DC]',
                    }[type] || 'bg-slate-100 text-slate-600';
                },

                downloadName() {
                    if (!this.materi) {
                        return 'materi';
                    }

                    const extension = this.materi.type === 'pdf' ? 'pdf' : 'file';
                    const safeTitle = String(this.materi.title || 'materi')
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/(^-|-$)/g, '');

                    return `${safeTitle || 'materi'}.${extension}`;
                },

                formatDate(value) {
                    if (!value) {
                        return '';
                    }

                    return new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    }).format(new Date(value));
                },

                resetPreviewState() {
                    this.pdfLoaded = false;
                    this.pdfFailed = false;
                    this.imageLoaded = false;
                    this.imageFailed = false;
                    this.youtubeFailed = false;
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
