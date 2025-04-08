<div class="p-4 text-right text-lg font-semibold text-gray-700 border-t">
    Total Gaji Bulan Ini: Rp {{ number_format($totalGaji, 0, ',', '.') }}
    <br>
    <a href="{{ $cetakUrl }}"
    target="_blank"
    title="Cetak Slip Gaji Bulanan"
    class="inline-flex items-center mt-2 px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 border border-gray-300">

     {{-- Ikon printer --}}
     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
         <path d="M6 2a1 1 0 00-1 1v2h10V3a1 1 0 00-1-1H6zM5 7a1 1 0 00-1 1v7a1 1 0 001 1h1v2a1 1 0 001 1h6a1 1 0 001-1v-2h1a1 1 0 001-1V8a1 1 0 00-1-1H5zm3 6h4v2H8v-2z" />
     </svg>

     <span>Cetak</span>
 </a>

</div>
