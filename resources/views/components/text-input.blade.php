@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-black focus:ring-black rounded-xl shadow-sm px-4 py-3']) }}>
