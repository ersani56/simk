<div class="p-4 border-t flex items-center justify-between">
    <div class="text-lg font-semibold text-gray-700">
        Total Gaji Bulan Ini: Rp {{ number_format($totalGaji, 0, ',', '.') }}
    </div>
    <a href="{{ $url }}" target="_blank"
       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 17h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-4 0H7a2 2 0 00-2 2v8a2 2 0 002 2h2m4 0v4m0-4H9m3 0h3"/>
        </svg>
        Cetak Gaji Bulan Ini
    </a>
</div>
<div class="p-4 text-right text-lg font-semibold text-gray-700 border-t">
    Total Gaji Bulan Ini: Rp {{ number_format($totalGaji, 0, ',', '.') }} <br>
    <a href="{{ route('slip-gaji.bulan', $bulan) }}" target="_blank"
        class="inline-block mt-2 px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
        Cetak Laporan PDF
    </a>
</div>

