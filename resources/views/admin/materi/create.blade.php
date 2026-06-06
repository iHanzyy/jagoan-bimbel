<x-layouts.app title="Upload Materi - Jagoan Bimbel">
    <section x-data="adminMateriCreate()" x-init="guardAdmin()" class="min-h-screen bg-[#F5F5F5]">
        <x-admin.navbar />

        <div class="mx-auto max-w-5xl px-5 py-8 sm:px-8 lg:px-10">
            <a href="/admin/materi" class="mb-4 inline-flex items-center text-sm font-bold text-[#1591DC] transition hover:text-[#2C5EAD]">
                &larr; Kembali ke daftar materi
            </a>

            <header class="mb-8 rounded-2xl border border-[#DCE6F2] bg-white p-6 shadow-[0_18px_45px_rgba(21,45,75,0.08)] lg:p-8">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm font-bold text-[#1591DC]">Dashboard Admin</p>
                        <h1 class="mt-2 text-3xl font-extrabold leading-tight text-[#001A41] sm:text-4xl">Upload Soal</h1>
                        <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-slate-600 sm:text-base">
                            Upload file PDF, gambar, atau simpan link Youtube.
                        </p>
                    </div>
                </div>
            </header>

            <div
                x-show="errorMessage"
                x-cloak
                x-transition
                class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
            >
                <span x-text="errorMessage"></span>
            </div>

            <form
                @submit.prevent="submit"
                class="overflow-hidden rounded-2xl border border-[#DCE6F2] bg-white shadow-[0_18px_45px_rgba(21,45,75,0.08)]"
            >
                <div class="border-b border-[#E5EDF6] px-5 py-5 sm:px-7">
                    <div class="flex items-center gap-3">
                        <span class="h-8 w-1.5 rounded-full bg-[#2C5EAD]"></span>
                        <h2 class="text-xl font-extrabold text-[#001A41]">Detail Soal</h2>
                    </div>
                </div>

                <div class="grid gap-5 px-5 py-6 sm:px-7 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-[#0F2747]">Judul Soal</label>
                        <input
                            type="text"
                            x-model="form.title"
                            class="h-12 w-full rounded-xl border border-[#D9E4F1] bg-white px-4 text-sm font-semibold text-[#001A41] outline-none transition placeholder:text-slate-400 focus:border-[#1591DC] focus:ring-4 focus:ring-[#1591DC]/10"
                            placeholder="Masukkan judul soal"
                            required
                        >
                        <p x-show="errors.title" x-cloak x-text="errors.title" class="mt-2 text-sm font-semibold text-red-600"></p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-[#0F2747]">Tipe Soal</label>
                        <div class="relative">
                            <select
                                x-model="form.type"
                                class="h-12 w-full cursor-pointer appearance-none rounded-xl border border-[#D9E4F1] bg-white px-4 pr-14 text-sm font-semibold text-[#001A41] outline-none transition focus:border-[#1591DC] focus:ring-4 focus:ring-[#1591DC]/10"
                                required
                            >
                                <option value="pdf">PDF</option>
                                <option value="image">Image</option>
                                <option value="youtube">Youtube</option>
                            </select>

                            <span class="pointer-events-none absolute right-5 top-1/2 flex -translate-y-1/2 text-[#001A41]">
                                <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                        <p x-show="errors.type" x-cloak x-text="errors.type" class="mt-2 text-sm font-semibold text-red-600"></p>
                    </div>

                    <div x-show="form.type === 'pdf' || form.type === 'image'" x-cloak class="md:col-span-2">
                        <label class="mb-2 block text-sm font-bold text-[#0F2747]">File</label>
                        <input
                            type="file"
                            @change="form.file = $event.target.files[0]"
                            :accept="form.type === 'pdf' ? 'application/pdf' : 'image/jpeg,image/png'"
                            class="w-full rounded-xl border border-[#D9E4F1] bg-white px-4 py-3 text-sm font-semibold text-[#001A41] outline-none transition file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-[#1591DC] file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-[#1591DC] focus:ring-4 focus:ring-[#1591DC]/10"
                        >
                        <p class="mt-2 text-xs font-semibold text-slate-500">
                            Maksimal 10MB. PDF untuk tipe PDF, JPG/PNG untuk tipe Image.
                        </p>
                        <p x-show="errors.file" x-cloak x-text="errors.file" class="mt-2 text-sm font-semibold text-red-600"></p>
                    </div>

                    <div x-show="form.type === 'youtube'" x-cloak class="md:col-span-2">
                        <label class="mb-2 block text-sm font-bold text-[#0F2747]">URL Youtube</label>
                        <input
                            type="url"
                            x-model="form.youtube_url"
                            class="h-12 w-full rounded-xl border border-[#D9E4F1] bg-white px-4 text-sm font-semibold text-[#001A41] outline-none transition placeholder:text-slate-400 focus:border-[#1591DC] focus:ring-4 focus:ring-[#1591DC]/10"
                            placeholder="https://www.youtube.com/watch?v=..."
                        >
                        <p x-show="errors.youtube_url" x-cloak x-text="errors.youtube_url" class="mt-2 text-sm font-semibold text-red-600"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-bold text-[#0F2747]">Deskripsi</label>
                        <textarea
                            x-model="form.description"
                            rows="5"
                            class="w-full resize-y rounded-xl border border-[#D9E4F1] bg-white px-4 py-3 text-sm font-semibold leading-6 text-[#001A41] outline-none transition placeholder:text-slate-400 focus:border-[#1591DC] focus:ring-4 focus:ring-[#1591DC]/10"
                            placeholder="Tulis deskripsi soal"
                            required
                        ></textarea>
                        <p x-show="errors.description" x-cloak x-text="errors.description" class="mt-2 text-sm font-semibold text-red-600"></p>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[#E5EDF6] bg-[#F8FBFE] px-5 py-5 sm:flex-row sm:justify-end sm:px-7">
                    <a href="/admin/materi" class="inline-flex items-center justify-center rounded-xl border border-[#BFD7EB] px-5 py-3 text-sm font-bold text-[#2C5EAD] transition hover:border-[#1591DC] hover:bg-[#4BB8FA]/10">
                        Batal
                    </a>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-[#1591DC] px-5 py-3 text-sm font-bold text-white shadow-[0_10px_20px_rgba(21,145,220,0.20)] transition hover:bg-[#2C5EAD] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span x-show="!loading">Upload</span>
                        <span x-show="loading" x-cloak>Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function adminMateriCreate() {
            return {
                loading: false,
                errorMessage: '',
                errors: {},
                form: {
                    title: '',
                    description: '',
                    type: 'pdf',
                    file: null,
                    youtube_url: '',
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

                buildFormData() {
                    const data = new FormData();

                    data.append('title', this.form.title);
                    data.append('description', this.form.description);
                    data.append('type', this.form.type);

                    if (this.form.type === 'youtube') {
                        data.append('youtube_url', this.form.youtube_url);
                    }

                    if ((this.form.type === 'pdf' || this.form.type === 'image') && this.form.file) {
                        data.append('file', this.form.file);
                    }

                    return data;
                },

                async submit() {
                    this.loading = true;
                    this.errors = {};
                    this.errorMessage = '';

                    try {
                        const response = await fetch('/api/upload-materi', {
                            method: 'POST',
                            headers: this.authHeaders(),
                            body: this.buildFormData(),
                        });

                        const payload = await response.json();

                        if (!response.ok) {
                            this.handleError(payload);
                            return;
                        }

                        await this.showSuccess('Upload berhasil!', payload.message || 'Materi berhasil diupload.');
                        window.location.href = '/admin/materi';
                    } catch (error) {
                        this.showError('Gagal terhubung ke server.');
                    } finally {
                        this.loading = false;
                    }
                },

                handleError(payload) {
                    const validationErrors = payload.errors || {};

                    this.errors = Object.fromEntries(
                        Object.entries(validationErrors).map(([field, messages]) => [
                            field,
                            Array.isArray(messages) ? messages[0] : messages,
                        ]),
                    );

                    this.showError(payload.message || 'Upload materi gagal.');
                },

                async logout() {
                    await fetch('/api/logout', {
                        method: 'POST',
                        headers: this.authHeaders(),
                    });

                    localStorage.clear();
                    window.location.href = '/login';
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
                        return Promise.resolve();
                    }

                    return window.Swal.fire({
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
