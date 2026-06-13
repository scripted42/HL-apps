<div>
  @section('title', 'Produk')

  <!-- Page header -->
  <div class="page-header d-print-none mb-4">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle text-secondary">Manajemen</div>
          <h2 class="page-title font-weight-black text-dark tracking-tight">Daftar Produk</h2>
        </div>
        <div class="col-auto ms-auto">
          <button wire:click="openCreateModal" class="btn btn-primary font-weight-bold">
            <i class="ti ti-plus me-1"></i> Tambah Produk
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Page body -->
  <div class="page-body">
    <div class="container-xl">
      
      @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible mb-4 shadow-xs" role="alert">
          <div class="d-flex">
            <div><i class="ti ti-circle-check me-2" style="font-size: 1.25rem;"></i></div>
            <div class="font-weight-medium">{{ session('message') }}</div>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
      @endif

      <div class="card shadow-xs">
        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <div class="text-secondary">
              Cari Produk:
              <div class="ms-2 d-inline-block">
                <input wire:model.live="search" type="search" class="form-control form-control-sm" placeholder="Ketik nama produk..." style="width: 240px;">
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table card-table table-vcenter text-nowrap">
            <thead>
              <tr class="text-muted uppercase font-weight-bold" style="font-size: 0.675rem; background-color: #fcfdfe;">
                <th>Nama Produk</th>
                <th class="text-center">Tipe Produk</th>
                <th class="text-end">Harga Modal</th>
                <th class="text-end">Harga Base (HET)</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $p)
                <tr>
                  <td class="font-weight-black text-dark" style="font-size: 0.95rem;">{{ $p->name }}</td>
                  <td class="text-center">
                    @if($p->type === 'LM')
                      <span class="badge bg-indigo-lt text-indigo font-weight-bold">LM</span>
                    @else
                      <span class="badge bg-purple-lt text-purple font-weight-bold">BR</span>
                    @endif
                  </td>
                  <td class="text-end font-weight-bold text-secondary">
                    Rp {{ number_format($p->harga_modal, 0, ',', '.') }}
                  </td>
                  <td class="text-end font-weight-black text-dark">
                    Rp {{ number_format($p->harga_base, 0, ',', '.') }}
                  </td>
                  <td class="text-center">
                    <div class="btn-list justify-content-center">
                      <button wire:click="openEditModal({{ $p->id }})" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-indigo" title="Edit Produk">
                        <i class="ti ti-edit"></i>
                      </button>
                      <button onclick="confirm('Apakah Anda yakin ingin menonaktifkan produk ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $p->id }})" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-danger" title="Nonaktifkan Produk">
                        <i class="ti ti-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-5 text-center text-muted">Belum ada data produk yang sesuai.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex align-items-center justify-content-between">
          {{ $products->links() }}
        </div>
      </div>

    </div>
  </div>

  <!-- Product Form Modal -->
  <div class="modal modal-blur fade" id="product-modal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content shadow-lg border-0">
        <div class="modal-header border-bottom">
          <h5 class="modal-title font-weight-black text-dark h3">{{ $isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form wire:submit.prevent="save">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label font-weight-semibold">Nama Produk <span class="text-danger">*</span></label>
              <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Ketik nama produk..." required>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
              <label class="form-label font-weight-semibold">Tipe Produk <span class="text-danger">*</span></label>
              <select wire:model="type" class="form-select @error('type') is-invalid @enderror" required>
                <option value="LM">LM</option>
                <option value="BR">BR</option>
              </select>
              @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-semibold">Harga Modal (Rupiah) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text font-weight-bold">Rp</span>
                <input wire:model="harga_modal" type="number" step="0.01" class="form-control @error('harga_modal') is-invalid @enderror" placeholder="Contoh: 150000" required>
              </div>
              @error('harga_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-semibold">Harga Base / HET (Rupiah) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text font-weight-bold">Rp</span>
                <input wire:model="harga_base" type="number" step="0.01" class="form-control @error('harga_base') is-invalid @enderror" placeholder="Contoh: 200000" required>
              </div>
              @error('harga_base') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-link link-secondary font-weight-semibold" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary font-weight-bold px-4">
              <i class="ti ti-device-floppy me-1"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalElement = document.getElementById('product-modal');
      const bsModal = new bootstrap.Modal(modalElement);

      window.addEventListener('show-product-modal', () => {
        bsModal.show();
      });

      window.addEventListener('hide-product-modal', () => {
        bsModal.hide();
      });
    });
  </script>
  @endpush
</div>
