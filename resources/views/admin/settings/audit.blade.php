@extends('admin.layouts.master')
@section('title', 'Audit Log Sistem')

@section('content')
<div class="space-y-6 animate-fadeIn">
    
    {{-- HEADER --}}
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="font-black text-slate-800 text-xl tracking-tight">Audit Log & Aktivitas</h2>
            <p class="text-xs text-slate-500 mt-1">Pantau dan telusuri seluruh riwayat aktivitas pengguna di dalam sistem HRIS.</p>
        </div>
        <div class="flex gap-3">
            <button class="bg-slate-50 text-slate-500 px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-100 hover:text-slate-700 transition-all flex items-center gap-2 border border-slate-200 shadow-sm">
                <i class="fas fa-file-export"></i> Export
            </button>
            <button onclick="window.location.reload()" class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-colors shadow-sm" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
        <form action="{{ route('admin.settings.audit') ?? '#' }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            {{-- Filter Tanggal --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal</label>
                <div class="relative">
                    <i class="fas fa-calendar-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                </div>
            </div>

            {{-- Filter Pengguna --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pengguna</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="user" value="{{ request('user') }}" placeholder="Nama pengguna..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>

            {{-- Filter Tipe Aktivitas --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipe Aktivitas</label>
                <div class="relative">
                    <i class="fas fa-tag absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    <select name="action" class="w-full pl-11 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 appearance-none cursor-pointer">
                        <option value="">Semua Aktivitas</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Create (Tambah)</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Update (Ubah)</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Delete (Hapus)</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login / Akses</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            {{-- Button Cari --}}
            <div class="flex items-end gap-2">
                @if(request()->anyFilled(['date', 'user', 'action']))
                    <a href="{{ route('admin.settings.audit') ?? '#' }}" class="bg-rose-50 text-rose-500 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-100 transition-all flex items-center justify-center border border-rose-100" title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> Terapkan
                </button>
            </div>
        </form>
    </div>

    {{-- DATA TABLE LOGS --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pengguna</th>
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Aktivitas</th>
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Modul / Detail</th>
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($logs ?? [] as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            
                            {{-- Kolom Waktu --}}
                            <td class="py-4 px-6 align-top">
                                <p class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($log->created_at ?? now())->translatedFormat('d M Y') }}</p>
                                <p class="text-xs font-medium text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($log->created_at ?? now())->format('H:i:s') }} WIB</p>
                            </td>

                            {{-- Kolom Pengguna --}}
                            <td class="py-4 px-6 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($log->causer->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-700">{{ $log->causer->name ?? 'System Automated' }}</p>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">{{ $log->causer->role ?? 'Sistem' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kolom Aktivitas --}}
                            <td class="py-4 px-6 align-top">
                                @php
                                    $action = strtolower($log->event ?? 'unknown');
                                    $bgColor = 'bg-slate-100'; $textColor = 'text-slate-600'; $icon = 'fa-info-circle';
                                    
                                    if(str_contains($action, 'create') || str_contains($action, 'store') || str_contains($action, 'approve') || str_contains($action, 'created')) {
                                        $bgColor = 'bg-emerald-50'; $textColor = 'text-emerald-600'; $icon = 'fa-plus-circle';
                                    } elseif(str_contains($action, 'update') || str_contains($action, 'edit') || str_contains($action, 'updated')) {
                                        $bgColor = 'bg-sky-50'; $textColor = 'text-sky-600'; $icon = 'fa-edit';
                                    } elseif(str_contains($action, 'delete') || str_contains($action, 'destroy') || str_contains($action, 'reject') || str_contains($action, 'deleted')) {
                                        $bgColor = 'bg-rose-50'; $textColor = 'text-rose-600'; $icon = 'fa-trash-alt';
                                    } elseif(str_contains($action, 'login')) {
                                        $bgColor = 'bg-purple-50'; $textColor = 'text-purple-600'; $icon = 'fa-sign-in-alt';
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider {{ $bgColor }} {{ $textColor }}">
                                    <i class="fas {{ $icon }}"></i> {{ $action }}
                                </span>
                            </td>

                            {{-- Kolom Deskripsi/Modul --}}
                            <td class="py-4 px-6 align-top">
                                <p class="font-bold text-slate-700 uppercase tracking-tight">{{ str_replace('_', ' ', $log->log_name ?? 'Sistem HRIS') }}</p>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-2">{{ $log->description ?? 'Melakukan perubahan pada data sistem.' }}</p>
                            </td>

                            {{-- Kolom Aksi (Lihat Detail JSON) --}}
                            <td class="py-4 px-6 align-top text-right">
                                @if(isset($log->properties) && count($log->properties) > 0)
                                    <button onclick="showAuditDetail({{ $log->id }})" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    
                                    {{-- Menyimpan Data JSON tersembunyi untuk modal --}}
                                    <div id="audit-data-{{ $log->id }}" class="hidden">
                                        @json($log->properties)
                                    </div>
                                @else
                                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        {{-- Tampilan Jika Data Kosong --}}
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <h3 class="font-black text-slate-700 text-lg">Belum Ada Log Aktivitas</h3>
                                <p class="text-sm text-slate-400 mt-1">Aktivitas pengguna di dalam sistem akan tercatat dan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- PAGINATION --}}
        @if(isset($logs) && $logs->hasPages())
        <div class="p-6 border-t border-slate-50 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL DETAIL AUDIT --}}
<div id="auditModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAuditDetail()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-slate-100">
            
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Detail Perubahan Data</h3>
                <button onclick="closeAuditDetail()" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all border border-slate-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="auditContent">
                    {{-- Konten JSON akan di-inject oleh JavaScript --}}
                </div>
            </div>
            
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end">
                <button onclick="closeAuditDetail()" class="bg-white text-slate-700 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-100 border border-slate-200 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showAuditDetail(logId) {
        const modal = document.getElementById('auditModal');
        const contentDiv = document.getElementById('auditContent');
        const rawData = document.getElementById('audit-data-' + logId).innerText;
        
        try {
            const properties = JSON.parse(rawData);
            let html = '';

            // Jika ada data 'old' (Data Lama)
            if (properties.old) {
                html += `
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-rose-100">
                            <i class="fas fa-history"></i> Data Lama
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs font-mono text-slate-600 break-all overflow-x-auto">
                            <pre class="whitespace-pre-wrap">${JSON.stringify(properties.old, null, 2)}</pre>
                        </div>
                    </div>
                `;
            }

            // Jika ada data 'attributes' (Data Baru)
            if (properties.attributes) {
                html += `
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                            <i class="fas fa-check-circle"></i> Data Baru
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs font-mono text-slate-600 break-all overflow-x-auto">
                            <pre class="whitespace-pre-wrap">${JSON.stringify(properties.attributes, null, 2)}</pre>
                        </div>
                    </div>
                `;
            }

            // Fallback jika properties bukan format old/attributes
            if (!properties.old && !properties.attributes) {
                 html += `
                    <div class="col-span-2 space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-indigo-100">
                            <i class="fas fa-code"></i> Payload / Detail
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs font-mono text-slate-600 break-all overflow-x-auto">
                            <pre class="whitespace-pre-wrap">${JSON.stringify(properties, null, 2)}</pre>
                        </div>
                    </div>
                `;
            }

            contentDiv.innerHTML = html;
            modal.classList.remove('hidden');
            
        } catch (e) {
            console.error("Error parsing JSON:", e);
            alert("Terjadi kesalahan saat membaca data log.");
        }
    }

    function closeAuditDetail() {
        document.getElementById('auditModal').classList.add('hidden');
    }
</script>
@endsection