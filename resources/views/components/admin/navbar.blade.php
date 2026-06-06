<nav class="bg-[#2C5EAD] text-white shadow-[0_8px_24px_rgba(44,94,173,0.16)]">
    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
        <a href="/admin/materi" class="text-xl font-extrabold tracking-tight">
            Admin Panel
        </a>

        <div class="flex items-center gap-2 sm:gap-3">
            <a href="/admin/materi" class="rounded-xl bg-white/15 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/20">
                Dashboard
            </a>

            <button
                type="button"
                @click="logout"
                class="cursor-pointer rounded-xl px-4 py-2 text-sm font-bold text-red-300 transition hover:bg-red-700/15 hover:text-white"
            >
                Logout
            </button>
        </div>
    </div>
</nav>
