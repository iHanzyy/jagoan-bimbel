@props([
    'eyebrow',
    'title',
    'description',
])

<header class="mb-8 flex flex-col gap-5 rounded-2xl border border-[#DCE6F2] bg-white p-6 shadow-[0_18px_45px_rgba(21,45,75,0.08)] md:flex-row md:items-center md:justify-between lg:p-8">
    <div>
        <p class="text-sm font-bold text-[#1591DC]">{{ $eyebrow }}</p>
        <h1 class="mt-2 text-3xl font-extrabold leading-tight text-[#001A41] sm:text-4xl">
            {{ $title }}
        </h1>
        <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-slate-600 sm:text-base">
            {{ $description }}
        </p>
    </div>

    @isset($actions)
        <div class="flex flex-col">
            {{ $actions }}
        </div>
    @endisset
</header>
