</div>
</div>
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>document.write(new Date().getFullYear())</script> © MK TRADERS
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    Developed by <i class="mdi mdi-heart text-danger"></i> SYNERGY INTEGRATED SOLUTIONS
                </div>
            </div>
        </div>
    </div>
</footer>

</div>

<div class="rightbar-overlay"></div>

<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

<!-- Icon -->
<script src="https://unicons.iconscout.com/release/v2.0.1/script/monochrome/bundle.js"></script>

<!-- apexcharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- Vector map-->
<script src="{{ asset('assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

<script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>

<!-- DataTables CSS/JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- DataTable Init -->
<script>
  $(document).ready(function () {
    $('#suppilerTable').DataTable({
      "pageLength": 10,
      "responsive": true,
      "autoWidth": false,
      // "order": [[1, "desc"]], 
      "columnDefs": [
        { "orderable": false, "targets": [0, 3] } 
      ]
    });
  });
</script>
<script>
  $(document).ready(function () {
    $('#payablesTable').DataTable({
      "pageLength": 10,
      "responsive": true,
      "autoWidth": false,
      // "order": [[1, "desc"]], 
      "columnDefs": [
        { "orderable": false, "targets": [0, 3] } 
      ]
    });
  });
</script>
<script>
  $(document).ready(function () {
    $('#DealerTable').DataTable({
      "pageLength": 10,
      "responsive": true,
      "autoWidth": false,
      // "order": [[1, "desc"]], 
      "columnDefs": [
        { "orderable": false, "targets": [0, 6] } 
      ]
    });
  });
</script>
<script>
  $(document).ready(function () {
    $('#receivablePaymentsTable').DataTable({
      "pageLength": 10,
      "responsive": true,
      "autoWidth": false,
      // "order": [[1, "desc"]], 
      "columnDefs": [
        { "orderable": false, "targets": [0, 5] } 
      ]
    });
  });
</script>
<script>
  $(document).ready(function () {
    $('#payablePaymentsTable').DataTable({
      "pageLength": 10,
      "responsive": true,
      "autoWidth": false,
      // "order": [[1, "desc"]], 
      "columnDefs": [
        { "orderable": false, "targets": [0, 5] } 
      ]
    });
  });
</script>
<script>
  $(document).ready(function () {
    $('#cashBookTable').DataTable({
      "pageLength": 10,
      "responsive": true,
      "autoWidth": false,
      "order": [[1, "desc"]], 
      "columnDefs": [
        { "orderable": false, "targets": [0, 7] } 
      ]
    });
  });
</script>
{{-- <script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#cashBookTable')) {
            $('#cashBookTable').DataTable().destroy();
        }
        $('#cashBookTable').DataTable({
            "pageLength": 10,
            "responsive": true,
            "autoWidth": false,
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "order": [[0, "desc"]], // Sort by Date (latest first)
            "columnDefs": [
                { "orderable": false, "targets": [5] }, // Cash, Online, Cheque not sortable
                { "type": "num", "targets": [2, 3, 4] }        // Proper number sorting
            ],
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    });
</script> --}}
@yield('scripts')
</body>
</html>
