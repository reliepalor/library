
@props(['highlight' => false])

<div class="flex justify-center">
    <div class="flex justify-between p-5 mb-2 w-[50vw] bg-white shadow-md rounded-lg" @class(['highlight' => $highlight, 'card']) >
        {{ $slot }}
        <a {{ $attributes}} class="btn border-2 border-green-300 hover:bg-green-100 p-2 rounded-lg">Veiw Details</a>
    </div>
</div>

