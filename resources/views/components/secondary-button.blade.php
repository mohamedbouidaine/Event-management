<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center px-4 py-2
                bg-red-500 dark:bg-red-500
                border border-red-600 dark:border-red-600
                rounded-md font-semibold text-xs text-white
                uppercase tracking-widest shadow-sm
                hover:bg-red-600 dark:hover:bg-red-600
                focus:outline-none focus:ring-2 focus:ring-red-500
                focus:ring-offset-2 dark:focus:ring-offset-gray-800
                disabled:opacity-25 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
